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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cancellation_status')->nullable(); // requested/approved/rejected
            $table->string('cancellation_reason')->nullable();  // mã lý do chuẩn hóa
            $table->text('cancellation_user_note')->nullable();  // ghi chú tự do của user
            $table->text('cancellation_admin_note')->nullable(); // phản hồi admin
            $table->timestamp('cancellation_requested_at')->nullable();
            $table->timestamp('cancellation_processed_at')->nullable();
            $table->unsignedBigInteger('cancellation_processed_by')->nullable(); // id admin/staff
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
