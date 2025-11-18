<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\ServiceBooking;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCancellationRequested;
use Inertia\Inertia;


class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard chính của user
     */

    public function index(Request $request)
    {
        $user = Auth::user();
        $ordersCount = $user->orders()->count();
        $bookingsCount = $user->service_bookings()->count();
        $unreadNotificationsCount = $user->unreadNotifications()->count();
        
        return Inertia::render('User/Dashboard', [
            'ordersCount' => $ordersCount,
            'bookingsCount' => $bookingsCount, 
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'pageTitle' => 'Dashboard',
            'pageDescription' => 'Welcome back! Here\'s your account overview',
        ]);
    }



    public function profileSettings()
    {
        return Inertia::render('User/ProfileSettings', [
            'pageTitle' => 'Cài đặt hồ sơ',
            'pageDescription' => 'Quản lý thông tin cá nhân và tùy chọn tài khoản của bạn',
        ]);
    }

    public function updateProfileSettings(Request $request)
    {
        $user = Auth::user();

        // Xử lý cập nhật thông tin cá nhân
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string',
            'district' => 'nullable|string',
            'ward' => 'nullable|string',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email này đã được sử dụng',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        try {
            // Cập nhật thông tin cơ bản
            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'],
                'province' => $validatedData['province'],
                'district' => $validatedData['district'],
                'ward' => $validatedData['ward'],
            ]);

            // Cập nhật mật khẩu nếu có
            if ($request->filled('current_password') && $request->filled('new_password')) {
                if (!\Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
                }

                $user->update(['password' => \Hash::make($request->new_password)]);
            }

            return back()->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }


    /**
     * Hiển thị trang đơn hàng của user
     */
    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->get()
            ->map(function ($order) {
                $order->append([
                    'ghn_status_text',     
                    'ghn_expected_delivery_formatted',
                ]);
                $order->is_shipping = $order->isShipping();

            return $order;
        });
        
        // Load images cho các đơn hàng cũ (nếu chưa có image)
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                // Nếu chưa có image, load từ item relationship
                if (empty($item->image) && $item->item_id && $item->item_type) {
                    try {
                        // Load trực tiếp dựa trên item_type và item_id
                        if ($item->item_type === 'medicine') {
                            $product = Medicine::find($item->item_id);
                        } elseif ($item->item_type === 'goods') {
                            $product = Goods::find($item->item_id);
                        }
                        
                        if ($product && isset($product->image)) {
                            $item->image = $product->image;
                        }
                    } catch (\Exception $e) {
                        // Bỏ qua nếu không load được
                        $item->image = null;
                    }
                }
            }
        }
        
        return Inertia::render('User/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function orderDetails(Request $request, $orderId)
    {
        $user = Auth::user();

        $order = $user->orders()
            ->with(['items'])
            ->where('id', $orderId)
            ->firstOrFail()
            ->append([
                'ghn_status_text',
                'ghn_expected_delivery_formatted',
            ]);

        return Inertia::render('User/Orders/Details', [
            'order' => $order,
        ]);
    }

    //yêu cầu hủy đơn hàng
    public function requestCancel(Request $request, Order $order)
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền thao tác trên đơn hàng này.');
        }

        $data = $request->validate([
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:2000',
        ]);

        try {
            DB::transaction(function () use ($order, $data) {
                $order = Order::whereKey($order->getKey())->lockForUpdate()->firstOrFail();
                //sử dụng lockForUpdate đảm bảo không có ai đồng thời thay đổi trạng thái
                if (! $order->isCancellable()) {
                    abort(422, 'Đơn hàng không thể yêu cầu hủy.');
                }

                if (!$order->order_status_before_cancellation) {
                    $order->order_status_before_cancellation = $order->order_status;
                }

                $order->order_status = Order::STATUS['CANCELLATION_REQUESTED'];
                $order->cancellation_status = Order::CANCELLATION_STATUS['REQUESTED'];
                $order->cancellation_reason = $data['reason'];
                $order->cancellation_user_note = $data['note'] ?? null;
                $order->cancellation_requested_at = now();
                $order->cancellation_processed_at = null;
                $order->cancellation_processed_by = null;
                $order->cancellation_admin_note = null;
                $order->save();
            });
        } catch (\Throwable $e) {
            return back()->withErrors([
                'error' => 'Không thể gửi yêu cầu hủy: ' . $e->getMessage()
            ]);
        }

        $order->refresh();

        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new OrderCancellationRequested($order));
        }

        return back()->with('success', 'Đã gửi yêu cầu hủy. Vui lòng chờ Admin xử lý.');
    }

    /**
     * Hiển thị danh sách dịch vụ đã đặt của user
     */
    public function services()
    {
        $user = Auth::user();
        
        $bookings = ServiceBooking::where('user_id', $user->id)
            ->with('service')
            ->latest()
            ->get();

        return Inertia::render('User/Services/Index', [
            'bookings' => $bookings,
            'pageTitle' => 'Dịch vụ đã đặt',
            'pageDescription' => 'Quản lý các dịch vụ bạn đã đặt qua hệ thống',
        ]);
    }

    /**
     * Hiển thị chi tiết dịch vụ đã đặt
     */
    public function serviceDetails(Request $request, $bookingId)
    {
        $user = Auth::user();
        $booking = ServiceBooking::where('user_id', $user->id)
            ->with(['service', 'user'])
            ->where('id', $bookingId)
            ->firstOrFail();
        
        return Inertia::render('User/Services/Details', [
            'booking' => $booking,
            'pageTitle' => 'Chi tiết dịch vụ',
            'pageDescription' => 'Thông tin chi tiết về dịch vụ bạn đã đặt',
        ]);
    }

    /**
     * Hiển thị thông báo của users
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);

        // Đánh dấu đã đọc khi vào trang
        $user->unreadNotifications->markAsRead();
        
        return Inertia::render('User/Notifications/Index', [
            'notifications' => $notifications,
            'pageTitle' => 'Thông báo',
            'pageDescription' => 'Quản lý các thông báo của bạn',
        ]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        return response()->json([
            'count' => $user->unreadNotifications()->count()
        ]);
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu đã đọc'
        ]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả đã đọc'
        ]);
    }

    public function deleteNotification($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thông báo'
        ]);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ]);

        $user = Auth::user();

        try {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            // Store new avatar
            $file = $request->file('avatar');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('avatars', $filename, 'public');

            // Update user record
            $user->update(['avatar' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully!',
                'avatar_url' => asset('storage/avatars/' . $filename)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * xóa ảnh
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        try {
            // Delete avatar file if exists
            if ($user->avatar) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
                $user->update(['avatar' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Avatar removed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Remove failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
