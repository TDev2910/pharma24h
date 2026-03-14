<?php

namespace App\Core\Auth\Application\Services;

use App\Core\Auth\Ports\Inbound\AuthUseCaseInterface;
use App\Core\Auth\Ports\Outbound\AuthRepositoryInterface;
use App\Core\Auth\Domain\DTOs\LoginData;
use App\Core\Auth\Domain\DTOs\SocialAuthData;
use Illuminate\Support\Facades\Auth;
use App\Core\Auth\Domain\DTOs\RegisterData;

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

    public function logout(): void
    {
        $this->authRepository->logout();
    }
}
