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

    public function findOrCreateSocialUser(\App\Core\Auth\Domain\DTOs\SocialAuthData $data)
    {
        $user = User::where('firebase_uid', $data->uid)
            ->orWhere('email', $data->email)
            ->first();

        if ($user) {
            // User exists - update info if needed
            if (!$user->firebase_uid) {
                $user->firebase_uid = $data->uid;
            }
            if (!$user->provider) {
                $user->provider = $data->provider;
            }
            if ($data->photoURL && !$user->avatar) {
                $user->avatar = $data->photoURL;
            }
            $user->email_verified_at = now();
            $user->save();
        } else {
            // Create new user
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => null,
                'avatar' => $data->photoURL,
                'firebase_uid' => $data->uid,
                'provider' => $data->provider,
                'role' => 'user',
                'email_verified_at' => now(),
            ]);
        }

        return $user;
    }
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
