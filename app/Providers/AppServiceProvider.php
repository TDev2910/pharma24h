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
    }

    public function boot(): void
    {
        // Định nghĩa Map đa hình
        Relation::enforceMorphMap([
            'medicine' => Medicine::class,
            'goods'    => Goods::class,
            'user'     => User::class,
        ]);

        // Load Routes (Giữ nguyên code cũ của bạn)
        if (file_exists(base_path('routes/web.php'))) {
            require base_path('routes/web.php');
        }
    }
}
