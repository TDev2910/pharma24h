<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Medicine;
use App\Models\Goods;
use App\Models\User;
// use App\Models\Service; // Bỏ comment nếu có model Service

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
