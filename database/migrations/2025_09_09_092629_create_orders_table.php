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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->string('delivery_method')->default('shipping'); // 'shipping' hoặc 'pickup'
            $table->string('pickup_location')->nullable(); // Tên cơ sở nhà thuốc để nhận hàng
            $table->string('shipping_address')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->decimal('total_amount', 12, 0);
            $table->string('payment_method'); // 'cod' hoặc 'vnpay'
            $table->string('payment_status')->default('pending'); // 'pending', 'paid', 'failed'
            $table->string('order_status')->default('new'); // 'new', 'pending', 'completed', 'cancelled'
            $table->string('transaction_id')->nullable(); // Mã giao dịch VNPAY
            $table->text('note')->nullable(); // Ghi chú đơn hàng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
