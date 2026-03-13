<?php

namespace App\Core\Auth\Ports\Inbound;

use App\Core\Auth\Domain\DTOs\LoginData;

interface AuthUseCaseInterface
{
    /**
     * Handle login attempt
     * 
     * @param LoginData $data
     * @return bool
     */
    public function login(LoginData $data): bool;

    /**
     * Handle logout
     * 
     * @return void
     */
    public function logout(): void;
}
