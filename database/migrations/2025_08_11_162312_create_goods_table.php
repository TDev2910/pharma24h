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
            // 1. Thông tin cơ bản
            $table->string('ma_hang')->nullable(); // Mã định danh nội bộ (có thể tự động hoặc nhập thủ công)
            $table->string('ma_vach')->nullable(); // Mã barcode/QR để quét khi bán hàng
            $table->string('ten_hang_hoa'); // Tên hiển thị của sản phẩm (bắt buộc)
            $table->foreignId('nhom_hang_id')->nullable()->constrained('product_categories'); // Phân loại sản phẩm
            
            // 2. Giá vốn, giá bán
            $table->decimal('gia_von', 10, 2)->default(0); // Giá nhập sản phẩm
            $table->decimal('gia_ban', 10, 2)->default(0); // Giá bán cho khách
            
            // 3. Tồn kho
            $table->boolean('quan_ly_theo_lo')->default(false); // Quản lý theo lô, hạn sử dụng
            $table->integer('ton_kho')->default(0); // Số lượng tồn kho hiện tại
            $table->integer('ton_thap_nhat')->default(0); // Định mức tồn thấp nhất
            $table->integer('ton_cao_nhat')->default(999999999); // Định mức tồn cao nhất
            
            // 4. Thông tin khác
            $table->string('quy_cach_dong_goi')->nullable(); // Cách đóng gói của sản phẩm
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers'); // Hãng sản xuất
            $table->string('nuoc_san_xuat')->nullable(); // Quốc gia nơi sản xuất
            
            // 5. Vị trí, trọng lượng
            $table->foreignId('position_id')->nullable()->constrained('positions'); // Nơi sắp xếp trong kho
            $table->decimal('trong_luong', 8, 2)->default(0); // Khối lượng sản phẩm
            $table->string('don_vi_tinh')->nullable(); // Đơn vị tính (tự nhập)
            
            // 6. Tùy chọn khác
            $table->boolean('ban_truc_tiep')->default(false); // Bán trực tiếp cho khách
            $table->text('mo_ta')->nullable(); // Mô tả sản phẩm
            $table->string('image')->nullable(); // Ảnh sản phẩm
            $table->integer('khach_dat')->default(0); // Số lượng khách đặt
            
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
