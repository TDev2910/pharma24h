<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\ServiceBooking;
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
        
        return Inertia::render('User/Dashboard', [
            'ordersCount' => $ordersCount,
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
            ->get();
        
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
            'orders' => $orders
        ]);
    }

    public function orderDetails(Request $request, $orderId)
    {
        $user = Auth::user();
        $order = $user->orders()->with(['items', 'user'])->where('id', $orderId)->firstOrFail();
             
        return Inertia::render('User/Orders/Details', [
            'order' => $order,
            'pageTitle' => 'Chi tiết đơn hàng',
        ]);
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
        // TODO: Lấy danh sách thông báo của user
        // $notifications = $user->notifications()->latest()->get();
        return view('user.dashboard.notifications', compact('user'));
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
