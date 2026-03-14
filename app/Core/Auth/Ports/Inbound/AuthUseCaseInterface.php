<?php

namespace App\Core\Auth\Ports\Inbound;

use App\Core\Auth\Domain\DTOs\LoginData;
use App\Core\Auth\Domain\DTOs\RegisterData;

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
    /**
     * Handle logout
     * 
     * @return void
     */
    public function logout(): void;
}
