<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kiểm tra nếu bảng đã tồn tại thì không tạo lại
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('ma_dich_vu')->nullable();
                $table->string('ten_dich_vu');
                $table->foreignId('nhom_hang_id')->nullable()->constrained('product_categories')->onDelete('set null');
                $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
                $table->decimal('gia_dich_vu', 10, 2)->default(0);
                $table->text('mo_ta')->nullable();
                $table->string('image')->nullable();
                $table->boolean('ban_truc_tiep')->default(false);
                $table->string('hinh_thuc')->default('tai_nha_thuoc');
                $table->integer('thoi_gian_thuc_hien')->nullable();
                $table->string('trang_thai')->default('kich_hoat');
                $table->text('ghi_chu')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};