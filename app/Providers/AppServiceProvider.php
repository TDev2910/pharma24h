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
            \App\Core\Doctor\Application\DoctorService::class
        );
        $this->app->bind(
            \App\Core\Doctor\Ports\Outbound\DoctorRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\DoctorRepository::class
        );
    }

    public function boot(): void
    {
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
