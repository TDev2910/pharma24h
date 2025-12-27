<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_fee')) {
                $table->integer('shipping_fee')->default(0)->after('total_amount')->comment('Phí ship thu của khách');
            }

            // Thêm cột ghn_fee (phí trả GHN) sau cột shipping_fee
            if (!Schema::hasColumn('orders', 'ghn_fee')) {
                $table->integer('ghn_fee')->default(0)->after('shipping_fee')->comment('Phí thực trả cho đơn vị vận chuyển');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_fee', 'ghn_fee']);
        });
    }
};
