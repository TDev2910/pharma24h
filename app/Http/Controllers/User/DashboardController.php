<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard chính của user
     */
     
    public function index(Request $request)
    {      
        $user = Auth::user();
        return view('user.dashboard.index', compact('user'));
    }

    public function profileSettings()
    {
        $user = Auth::user();
        return view('user.profile.profile-settings', compact('user'));
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
        // TODO: Lấy danh sách đơn hàng của user
        // $orders = $user->orders()->latest()->get();
        return view('user.dashboard.orders', compact('user'));
    }

    /**
     * Hiển thị hồ sơ sức khỏe của user
     */
    public function healthProfile()
    {
        $user = Auth::user();
        // TODO: Lấy thông tin sức khỏe của user
        return view('user.dashboard.health-profile', compact('user'));
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
