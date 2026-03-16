<?php

namespace App\Core\Auth\Application\Services;

use App\Core\Auth\Ports\Inbound\AuthUseCaseInterface;
use App\Core\Auth\Ports\Outbound\AuthRepositoryInterface;
use App\Core\Auth\Domain\DTOs\LoginData;
use App\Core\Auth\Domain\DTOs\SocialAuthData;
use Illuminate\Support\Facades\Auth;
use App\Core\Auth\Domain\DTOs\RegisterData;
use App\Core\Auth\Domain\DTOs\ForgetPasswordData;
use App\Core\Auth\Domain\DTOs\VerifyOtpData;
use App\Core\Auth\Domain\DTOs\ResetPasswordData;
use App\Core\Auth\Domain\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class AuthService implements AuthUseCaseInterface
{
    public function __construct(
        private readonly AuthRepositoryInterface $authRepository
    ) {}

    public function login(LoginData $data): bool
    {
        return $this->authRepository->attempt([
            'email' => $data->email,
            'password' => $data->password
        ], $data->remember);
    }

    public function register(RegisterData $data)
    {
        return $this->authRepository->register($data);
    }

    public function socialLogin(SocialAuthData $data): bool
    {
        $user = $this->authRepository->findOrCreateSocialUser($data);
        if ($user) {
            Auth::login($user);
            return true;
        }
        return false;
    }

    public function sendOtp(ForgetPasswordData $data)
    {
        $user = $this->authRepository->findByEmail($data->email);
        if(!$user) {
            throw new \Exception('Email không tồn tại trong hệ thống.');
        }
        $otp = $this->authRepository->createOtp($data->email);  
        Mail::to($data->email)->send(new OtpMail($otp));

    }

    public function verifyOtp(VerifyOtpData $data)
    {
        if (!$this->authRepository->verifyOtp($data->email, $data->otp)) {
            throw new \Exception('Mã OTP không chính xác hoặc đã hết hạn.');
        }
        return true;
    }

    public function resetPassword(ResetPasswordData $data)
    {
        // Kiểm tra OTP lại lần cuối trước khi đổi pass
        if (!$this->authRepository->verifyOtp($data->email, $data->otp)) {
            throw new \Exception('Xác thực không hợp lệ, vui lòng thử lại.');
        }

        if (!$this->authRepository->updatePassword($data->email, $data->password)) {
            throw new \Exception('Không thể cập nhật mật khẩu, vui lòng thử lại.');
        }

        return true;
    }

    public function logout(): void
    {
        $this->authRepository->logout();
    }
}
