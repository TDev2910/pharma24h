<?php

namespace App\Core\Auth\Ports\Outbound;

use App\Core\Auth\Domain\DTOs\RegisterData;
use App\Core\Auth\Domain\DTOs\SocialAuthData;

interface AuthRepositoryInterface
{
    /**
     * Attempt to authenticate with credentials
     * 
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt(array $credentials, bool $remember): bool;
    public function register(RegisterData $data);
    public function findOrCreateSocialUser(SocialAuthData $data);
    public function findByEmail(string $email): ?\App\Models\User;
    public function createOtp(string $email): string;
    public function verifyOtp(string $email, string $otp): bool;
    public function updatePassword(string $email, string $password): bool;
    public function logout(): void;
}
