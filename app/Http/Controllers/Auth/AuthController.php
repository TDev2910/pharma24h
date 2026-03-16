<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\Firebase\FirebaseService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginGoogleRequest;
use App\Core\Auth\Ports\Inbound\AuthUseCaseInterface;
use App\Core\Auth\Domain\DTOs\LoginData;
use App\Core\Auth\Domain\DTOs\RegisterData;
use App\Core\Auth\Domain\DTOs\SocialAuthData;
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
        $this->authUseCase->logout(); 
        return redirect('/')->with('success', 'Đăng xuất thành công!');
    }
    public function googleLogin(LoginGoogleRequest $request)
    {
        try {
            $validated = $request->validated();
            $socialData = SocialAuthData::fromRequest($validated);
            
            if ($this->authUseCase->socialLogin($socialData)) {
                $request->session()->regenerate(); 
                $user = Auth::user();
                $redirectUrl = '/';
                
                if ($user->role === 'admin') {
                    $redirectUrl = '/admin/admindashboard';
                } elseif ($user->role === 'staff' && $user->employee) {
                    $redirectUrl = '/staff/dashboard';
                }

                return redirect($redirectUrl);
            }

            return back()->withErrors(['error' => 'Đăng nhập thất bại']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi đăng nhập: ' . $e->getMessage()]);
        }
    }
}
