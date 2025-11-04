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
        Schema::create('employee_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('activity_type')->comment('Vd: Bán sản phẩm, Dịch vụ Spa');
            $table->decimal('target_amount', 15, 2)->comment('Chỉ tiêu (X)');
            $table->enum('bonus_type', ['fixed', 'percent']);
            $table->decimal('bonus_value', 15, 2)->comment('Thưởng (Y)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_targets');
    }
};
