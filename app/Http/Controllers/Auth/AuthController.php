<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\Firebase\FirebaseService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Core\Auth\Ports\Inbound\AuthUseCaseInterface;
use App\Core\Auth\Domain\DTOs\LoginData;
use App\Core\Auth\Domain\DTOs\RegisterData;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthUseCaseInterface $authUseCase
    ) {}

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
            'success' => session('success'),
        ]);
    }

    /**
     * Handle login request
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $loginData = LoginData::fromRequest($request);

        if ($this->authUseCase->login($loginData)) {
            $request->session()->regenerate(); 
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended('/admin/admindashboard')->with('success', 'Chào mừng Admin!');
            }

            if ($user->role === 'staff') {
                $employee = $user->employee ?? null;
                if ($employee) {
                    return redirect()->intended('/staff/dashboard')->with('success', 'Chào mừng ' . $user->name . '!');
                }
                return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
            }

            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput();
    }

    public function showRegistrationForm()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle registration request
     */
   public function register(RegisterRequest $request)
    {
        $dto = $request->toDTO();
        
        if ($this->authUseCase->register($dto)) {
            return redirect('/')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với MediAid!');
        }
        return back()->withErrors(['email' => 'Có lỗi xảy ra, vui lòng thử lại sau.'])->withInput();
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
