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
        Schema::create('stock_imports', function (Blueprint $table) {
            $table->id();
            $table->string('import_code')->unique(); // Mã nhập hàng
            $table->foreignId('supplier_id')->constrained('suppliers'); // Nhà cung cấp
            $table->date('import_date'); // Ngày nhập hàng
            $table->string('status')->default('pending'); // Trạng thái
            $table->decimal('total_amount', 15, 2)->default(0);// Tổng tiền
            $table->decimal('paid_amount',15,2)->default(0); // Tiền đã trả
            $table->decimal('remaining_amount',15,2)->default(0); // Tiền còn lại
            $table->text('note')->nullable(); // Ghi chú
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_imports');
    }
};
