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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('ten_nha_cung_cap',255)->comment('Tên nhà cung cấp');
            $table->string('ma_nha_cung_cap',255)->unique()->comment('Mã nhà cung cấp');
            $table->string('dien_thoai',15)->comment('Số điện thoại');
            $table->string('email',100)->nullable()->unique()->comment('Email');
            $table->text('dia_chi')->comment('Địa chỉ');
            $table->string('khu_vuc',100)->comment('Tỉnh thành - Quận/Huyện');
            $table->string('phuong_xa',100)->comment('Phường/Xã');
            $table->foreignId('nhom_nha_cung_cap_id')->constrained('supplier_categories');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú');
            $table->string('ten_cong_ty', 255)->nullable()->unique()->comment('Tên công ty');
            $table->string('ma_so_thue',20)->nullable()->unique()->comment('Mã số thuế');
            $table->enum('trang_thai',['active','inactive'])->default('active')->comment('Trạng thái');
            $table->index('ten_nha_cung_cap'); //tim theo ten nha cung cap
            $table->index('ma_nha_cung_cap'); //tim theo ma nha cung cap
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
