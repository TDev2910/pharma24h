<?php

namespace App\Core\Auth\Ports\Outbound;

use App\Core\Auth\Domain\DTOs\RegisterData;

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

    /**
     * Logout current user
     * 
     * @return void
     */
    public function logout(): void;
}
