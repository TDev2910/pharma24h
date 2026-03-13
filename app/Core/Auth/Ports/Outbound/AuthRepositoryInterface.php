<?php

namespace App\Core\Auth\Ports\Outbound;

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

    /**
     * Logout current user
     * 
     * @return void
     */
    public function logout(): void;
}
