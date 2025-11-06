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
        // Kiểm tra nếu bảng đã tồn tại thì không tạo lại
        if (!Schema::hasTable('service_bookings')) {
            Schema::create('service_bookings', function (Blueprint $table) {
                $table->id(); // Khóa chính 
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('customer_name'); // Tên khách hàng (bắt buộc)
                $table->string('customer_phone'); // SĐT khách hàng (bắt buộc)
                $table->string('customer_email')->nullable(); // Email khách hàng (không bắt buộc)
                $table->date('booking_date'); // Ngày khách hẹn đến
                $table->time('booking_time'); // Giờ khách hẹn đến
                $table->decimal('price', 10, 2); // Giá dịch vụ tại thời điểm đặt
                $table->string('payment_method')->default('pay_at_pharmacy'); // Phương thức thanh toán
                $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid'); // Trạng thái thanh toán
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending'); // Trạng thái lịch hẹn
                $table->text('notes')->nullable(); // Ghi chú của khách hàng
                $table->timestamps(); // created_at, updated_at
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
