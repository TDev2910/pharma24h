<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_import_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_import_id')->constrained()->onDelete('cascade');
            $table->string('product_type'); // 'medicine' hoặc 'goods'
            $table->foreignId('product_id'); // ID của medicine hoặc goods
            $table->integer('quantity'); // Số lượng nhập
            $table->decimal('unit_price', 10, 2); // Giá đơn vị
            $table->decimal('discount', 10, 2)->default(0); // Giảm giá
            $table->decimal('total_price', 15, 2); // Tổng tiền
            $table->text('note')->nullable(); // Ghi chú
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_import_items');
    }
};