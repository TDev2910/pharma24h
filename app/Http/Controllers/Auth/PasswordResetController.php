<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Core\Auth\Ports\Inbound\AuthUseCaseInterface;
use App\Core\Auth\Domain\DTOs\ForgetPasswordData;
use App\Core\Auth\Domain\DTOs\VerifyOtpData;
use App\Core\Auth\Domain\DTOs\ResetPasswordData;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function __construct(
        private readonly AuthUseCaseInterface $authUseCase
    ) {}

    /**
     * Show form to request OTP
     */
    public function showEmailForm()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
            'success' => session('success'),
        ]);
    }

    /**
     * Handle sending OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $dto = ForgetPasswordData::fromRequest($request);
            $this->authUseCase->sendOtp($dto);

            return redirect()->route('password.verify')
                ->with('success', 'Mã OTP đã được gửi đến email của bạn!')
                ->with('email', $request->email);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * Show form to verify OTP
     */
    public function showVerifyForm()
    {
        return Inertia::render('Auth/VerifyOtp', [
            'email' => session('email') ?? '',
        ]);
    }

    /**
     * Handle OTP verification
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $dto = VerifyOtpData::fromRequest($request);
            $this->authUseCase->verifyOtp($dto);

            return redirect()->route('password.reset')
                ->with('success', 'Xác thực thành công! Vui lòng đặt mật khẩu mới.')
                ->with('email', $request->email)
                ->with('otp', $request->otp);
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => $e->getMessage()]);
        }
    }

    /**
     * Show rest password form
     */
    public function showResetForm()
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => session('email') ?? '',
            'otp' => session('otp') ?? '',
        ]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $dto = ResetPasswordData::fromRequest($request);
            $this->authUseCase->resetPassword($dto);

            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.');
        } catch (\Exception $e) {
            return back()->withErrors(['password' => $e->getMessage()]);
        }
    }

    public function showPhoneVerifyForm() { return back(); }
    public function verifyPhoneOtp() { return back(); }
    public function handlePhoneVerification() { return back(); }
    public function savePhoneToSession() { return back(); }
    public function resetPhoneOtpAttempts() { return back(); }
}
