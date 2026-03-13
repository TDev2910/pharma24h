<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Core\Auth\Ports\Outbound\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Core\Auth\Domain\DTOs\RegisterData;

class AuthRepository implements AuthRepositoryInterface
{
    public function attempt(array $credentials, bool $remember): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    public function register(RegisterData $data)
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => bcrypt($data->password),
            'phone' => $data->phone,
            'address' => $data->address,
            'role' => 'user',
        ]);

        if ($user) {
            Auth::login($user);
            return true;
        }
        
        return false;
    }
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
