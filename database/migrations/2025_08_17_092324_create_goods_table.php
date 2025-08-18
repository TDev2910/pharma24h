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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hang')->unique()->nullable(); // Unique mã hàng
            $table->string('ma_vach')->unique()->nullable(); // Unique mã vạch
            $table->string('ten_hang_hoa');
            $table->foreignId('nhom_hang_id')->nullable()->constrained('product_categories');
            $table->decimal('gia_von', 10, 2)->default(0);
            $table->decimal('gia_ban', 10, 2)->default(0);
            $table->boolean('quan_ly_theo_lo')->default(false);
            $table->integer('ton_kho')->default(0);
            $table->integer('ton_thap_nhat')->default(0);
            $table->integer('ton_cao_nhat')->default(999999999);
            $table->string('quy_cach_dong_goi')->nullable();
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers');
            $table->string('nuoc_san_xuat')->nullable();
            $table->foreignId('position_id')->nullable()->constrained('positions');
            $table->decimal('trong_luong', 8, 2)->default(0);
            $table->string('don_vi_tinh')->nullable();
            $table->boolean('ban_truc_tiep')->default(false);
            $table->text('mo_ta')->nullable();
            $table->string('image')->nullable();
            $table->integer('khach_dat')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};
