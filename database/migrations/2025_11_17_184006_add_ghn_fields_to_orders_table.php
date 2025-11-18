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
            $table->string('ghn_order_code')->nullable()->unique()->after('order_code'); //mã đơn hàng ví dụ " GHNtestdemo"
            $table->string('ghn_status')->nullable()->after('ghn_order_code'); //trạng thái đơn hàng ví dụ " PENDING"
            $table->decimal('ghn_fee', 12, 0)->nullable()->after('ghn_status'); //phí giao hàng ví dụ " 10000"
            $table->timestamp('ghn_expected_delivery_time')->nullable()->after('ghn_fee'); //thời gian dự kiến giao hàng" 2025-01-01 10:00:00"
            $table->string('ghn_tracking_url')->nullable()->after('ghn_expected_delivery_time'); //url tracking đơn hàng ví dụ " https://ghn.vn/tracking/GHNtestdemo"
            $table->decimal('ghn_cod_amount', 12, 0)->nullable()->after('ghn_tracking_url'); //số tiền thanh toán COD ví dụ " 10000"
            $table->string('ghn_shipper_name')->nullable()->after('ghn_cod_amount'); //tên người giao hàng ví dụ " Hồ Công Thiên Đạt "
            $table->string('ghn_shipper_phone')->nullable()->after('ghn_shipper_name'); //số điện thoạng người giao hàng ví dụ " 0909090909"
            $table->timestamp('ghn_created_at')->nullable()->after('ghn_shipper_phone'); //thời gian tạo đơn hàng ví dụ " 2025-01-01 10:00:00"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'ghn_order_code',
                'ghn_status',
                'ghn_fee',
                'ghn_expected_delivery_time',
                'ghn_tracking_url',
                'ghn_cod_amount',
                'ghn_shipper_name',
                'ghn_shipper_phone',
                'ghn_created_at',
            ]);
        });
    }
};
