<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Core\Customer\Ports\Outbound\CustomerRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\CustomerRepository::class
        );
        $this->app->bind(
            \App\Core\Customer\Ports\Inbound\CustomerUseCaseInterface::class,
            \App\Core\Customer\Application\Services\CustomerService::class
        );
        $this->app->bind(
            \App\Core\Order\Ports\Outbound\OrderRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\OrderRepository::class
        );
        $this->app->bind(
            \App\Core\Order\Ports\Inbound\OrderUseCaseInterface::class,
            \App\Core\Order\Application\Services\OrderService::class
        );
        $this->app->bind(
            \App\Core\Doctor\Ports\Inbound\DoctorUseCaseInterface::class,
            \App\Core\Doctor\Application\Services\DoctorService::class
        );
        $this->app->bind(
            \App\Core\Doctor\Ports\Outbound\DoctorRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\DoctorRepository::class
        );
        $this->app->bind(
            \App\Core\Auth\Ports\Outbound\AuthRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\AuthRepository::class
        );
        $this->app->bind(
            \App\Core\Auth\Ports\Inbound\AuthUseCaseInterface::class,
            \App\Core\Auth\Application\Services\AuthService::class
        );

        // Binds Chat Module
        $this->app->bind(
            \App\Core\Chat\Ports\Inbound\ChatPortInterface::class,
            \App\Core\Chat\Application\Services\ChatService::class
        );

        $this->app->bind(
            \App\Core\Chat\Ports\Outbound\ChatRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\ChatRepository::class
        );

        $this->app->bind(
            \App\Core\Chat\Ports\Outbound\BroadcastNotificationPortInterface::class,
            \App\Infrastructure\Broadcasting\PusherBroadcastAdapter::class
        );

        // Binds Payment Module
        $this->app->bind(
            \App\Core\Payment\Ports\Inbound\PaymentUseCaseInterface::class,
            \App\Core\Payment\Application\Services\PaymentUseCase::class
        );

        // Binds Medicine Module
        $this->app->bind(
            \App\Core\Products\Medicine\Ports\Inbound\MedicineUseCaseInterface::class,
            \App\Core\Products\Medicine\Application\Services\MedicineService::class
        );
        $this->app->bind(
            \App\Core\Products\Medicine\Ports\Outbound\MedicineRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\MedicineRepository::class
        );
    }

    public function boot(): void
    {
        // Define Rate Limiter for Login SaaS
        \Illuminate\Support\Facades\RateLimiter::for('login-saas', function (\Illuminate\Http\Request $request) {
            $email = (string) $request->input('email');
            $key = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($email) . '|' . $request->ip());
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($key);
        });

        // Định nghĩa Map đa hình
        Relation::enforceMorphMap([
            'medicine' => Medicine::class,
            'goods'    => Goods::class,
            'user'     => User::class,
        ]);

        if (file_exists(base_path('routes/web.php'))) {
            require base_path('routes/web.php');
        }
    }
}
