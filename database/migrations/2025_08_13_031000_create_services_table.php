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
            $table->string('ma_dich_vu')->unique()->comment('Mã dịch vụ');
            $table->string('ten_dich_vu')->comment('Tên dịch vụ');
            $table->foreignId('nhom_dich_vu_id')->nullable()->constrained('product_categories')->comment('Nhóm dịch vụ');
            $table->decimal('gia_ban', 15, 2)->comment('Giá bán');
            $table->text('mo_ta')->nullable()->comment('Mô tả dịch vụ');
            $table->enum('hinh_thuc', ['tai_nha_thuoc', 'tai_nha_khach'])->default('tai_nha_thuoc')->comment('Hình thức dịch vụ');
            $table->integer('thoi_gian_thuc_hien')->nullable()->comment('Thời gian thực hiện (phút)');
            $table->enum('trang_thai', ['kich_hoat', 'tam_ngung', 'luu_tam'])->default('luu_tam')->comment('Trạng thái');
            $table->string('image')->nullable()->comment('Hình ảnh minh họa');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú');
            $table->foreignId('created_by')->nullable()->constrained('users')->comment('Người tạo');
            $table->foreignId('updated_by')->nullable()->constrained('users')->comment('Người cập nhật');
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
