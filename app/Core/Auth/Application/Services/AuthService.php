<?php

namespace App\Core\Auth\Application\Services;

use App\Core\Auth\Ports\Inbound\AuthUseCaseInterface;
use App\Core\Auth\Ports\Outbound\AuthRepositoryInterface;
use App\Core\Auth\Domain\DTOs\LoginData;

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

    public function logout(): void
    {
        $this->authRepository->logout();
    }
}
