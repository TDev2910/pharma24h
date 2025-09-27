<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_import_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_import_id')->constrained()->onDelete('cascade');
            $table->string('payment_method')->default('cash'); // cash, card, transfer
            $table->decimal('amount', 15, 2); // Số tiền thanh toán
            $table->text('note')->nullable(); // Ghi chú
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_import_payments');
    }
};