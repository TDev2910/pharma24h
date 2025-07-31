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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hang')->nullable();
            $table->string('ma_vach')->nullable();
            $table->string('ten_thuoc');
            $table->string('ten_viet_tat')->nullable();
            $table->foreignId('nhom_hang_id')->nullable()->constrained('product_categories');
            $table->decimal('gia_von', 10, 2)->default(0);
            $table->decimal('gia_ban', 10, 2)->default(0);
            $table->string('so_dang_ky')->nullable();
            $table->string('hoat_chat')->nullable();
            $table->string('ham_luong')->nullable();
            $table->foreignId('drugusage_id')->nullable()->constrained('drug_routes');
            $table->string('quy_cach_dong_goi')->nullable();
            $table->foreignId('manufacturer')->nullable()->constrained('manufacturers');
            $table->string('nuoc_san_xuat')->nullable();
            $table->integer('ton_thap_nhat')->default(0);
            $table->integer('ton_cao_nhat')->default(999999999);
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
        Schema::dropIfExists('medicines');
    }
};
