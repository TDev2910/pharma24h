<?php

namespace App\Core\Auth\Ports\Inbound;

use App\Core\Auth\Domain\DTOs\LoginData;
use App\Core\Auth\Domain\DTOs\RegisterData;
use App\Core\Auth\Domain\DTOs\ForgetPasswordData;
use App\Core\Auth\Domain\DTOs\VerifyOtpData;
use App\Core\Auth\Domain\DTOs\ResetPasswordData;

interface AuthUseCaseInterface
{
    /**
     * Handle login attempt
     * 
     * @param LoginData $data
     * @return bool
     */
    public function login(LoginData $data): bool;
    public function register(RegisterData $data);
    public function socialLogin(\App\Core\Auth\Domain\DTOs\SocialAuthData $data): bool;
    public function sendOtp(ForgetPasswordData $data);
    public function verifyOtp(VerifyOtpData $data);
    public function resetPassword(ResetPasswordData $data);
    /**
     * Handle logout
     * 
     * @return void
     */
    public function logout(): void;
}
