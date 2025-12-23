<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\Firebase\FirebaseService;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validated = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required', 'min:8'],
            ],

            //thông báo lỗi xác thực
            [
                'email.required' => 'Trường email là bắt buộc.',
                'email.email' => 'Trường email phải là một địa chỉ email hợp lệ.',
                'password.required' => 'Trường mật khẩu là bắt buộc.',
                'password.min' => 'Trường mật khẩu phải có ít nhất 8 ký tự.',
            ]
        );

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();

            // kiểm tra role trực tiếp
            if ($user->role === 'admin') {
                return redirect('/admin/admindashboard')->with('success', 'Chào mừng Admin!');
            }

            // Redirect staff về trang quản lý nhân viên hoặc dashboard riêng
            if ($user->role === 'staff') {
                // Kiểm tra xem user có liên kết với employee không
                $employee = $user->employee ?? null;
                if ($employee) {
                    return redirect('/staff/dashboard')->with('success', 'Chào mừng ' . $user->name . '!');
                }
                return redirect('/')->with('success', 'Đăng nhập thành công!');
            }

            // Redirect về Inertia route với Inertia response
            return redirect('/admin/admindashboard')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput();
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'confirmed', 'min:8'],
                'phone' => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
                'address' => ['nullable', 'string', 'max:255'],
            ],

            //thông báo lỗi xác thực
            [
                'name.required' => 'Trường họ và tên là bắt buộc.',
                'email.required' => 'Trường email là bắt buộc.',
                'email.email' => 'Trường email phải là một địa chỉ email hợp lệ.',
                'email.unique' => 'Email đã được sử dụng.',
                'password.required' => 'Trường mật khẩu là bắt buộc.',
                'password.min' => 'Trường mật khẩu phải có ít nhất 8 ký tự.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
                'phone.required' => 'Trường số điện thoại là bắt buộc.',
                'phone.regex' => 'Số điện thoại phải có 10-11 chữ số.',
                'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
                'address.max' => 'Địa chỉ không được quá 255 ký tự.',
            ]
        );

        // Tạo user mới với role mặc định là 'user'
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'address' => $request->address, // Có thể null
            'role' => 'user', // Luôn mặc định là user
        ];

        $user = User::create($userData);
        Auth::login($user);

        return redirect('/')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với MediAid!');
    }

    /**
     * Handle logout request
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Đăng xuất thành công!');
    }
    /**
     * Handle Google login request
     */
    //Đăng nhập bằng google
    public function googleLogin(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
            'uid' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'photoURL' => 'nullable|string',
        ]);

        $uid = $request->uid;
        $email = $request->email;
        $name = $request->name;
        $photoURL = $request->photoURL;

        try {
            //Tìm user theo firebase_uid hoặc email 
            //Để thực hiện tính năng Merge Account (Gộp tài khoản). 
            //Nếu người dùng từng đăng ký thủ công bằng abc@gmail.com,
            //hôm nay họ chọn "Login with Google" (cũng là abc@gmail.com), 
            //hệ thống sẽ hiểu là cùng 1 người.
            $user = User::where('firebase_uid', $uid)
                ->orWhere('email', $email)
                ->first();

            if ($user) {
                // User đã tồn tại - cập nhật thông tin
                if (!$user->firebase_uid) {
                    $user->firebase_uid = $uid;
                }
                if (!$user->provider) {
                    $user->provider = 'google';
                }
                if ($photoURL && !$user->avatar) {
                    $user->avatar = $photoURL;
                }
                $user->email_verified_at = now();
                $user->save();
            } else {
                // Tạo user mới
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => null, // Google user không cần password
                    'avatar' => $photoURL,
                    'firebase_uid' => $uid,
                    'provider' => 'google',
                    'role' => 'user',
                    'email_verified_at' => now(), //xác thực email trực tiếp
                ]);
            }

            // Đăng nhập user
            Auth::login($user);

            // Xác định redirect URL theo role
            $redirectUrl = '/';
            $message = 'Đăng nhập thành công!';
            
            if ($user->role === 'admin') {
                $redirectUrl = '/admin/admindashboard';
                $message = 'Chào mừng Admin!';
            } elseif ($user->role === 'staff') {
                $employee = $user->employee ?? null;
                if ($employee) {
                    $redirectUrl = '/staff/dashboard';
                    $message = 'Chào mừng ' . $user->name . '!';
                }
            }

            // Nếu là AJAX request, trả về JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => $redirectUrl,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ]
                ]);
            }

            // Nếu là form submit thông thường, redirect
            return redirect($redirectUrl)->with('success', $message);
            
        } catch (\Exception $e) {
            // Xử lý lỗi
            $errorMessage = 'Có lỗi xảy ra khi đăng nhập: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }
}
