<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Core\Auth\Ports\Outbound\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    public function attempt(array $credentials, bool $remember): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
