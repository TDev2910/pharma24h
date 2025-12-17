<?php

namespace App\Services\Firebase;

use Illuminate\Support\ServiceProvider;

/**
 * Firebase Service Provider
 * 
 * Đăng ký Firebase services vào Laravel container
 */
class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });

        $this->app->alias(FirebaseService::class, 'firebase');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}