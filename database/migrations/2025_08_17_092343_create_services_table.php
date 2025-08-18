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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hang')->unique()->nullable(); // Unique mã hàng
            $table->string('ma_vach')->unique()->nullable(); // Unique mã vạch
            $table->string('ten_dich_vu');
            $table->foreignId('nhom_hang_id')->nullable()->constrained('product_categories');
            $table->decimal('gia_dich_vu', 10, 2)->default(0);
            $table->text('mo_ta')->nullable();
            $table->string('image')->nullable();
            $table->boolean('ban_truc_tiep')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
