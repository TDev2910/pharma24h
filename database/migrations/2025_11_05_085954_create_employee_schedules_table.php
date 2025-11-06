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
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')  //liên kết với bảng employees
                ->constrained('employees')
                ->onDelete('cascade'); 

            $table->foreignId('shift_id') //liên kết với bảng shifts
                ->constrained('shifts')
                ->onDelete('cascade');

            $table->date('schedule_date'); //ngày làm việc
            $table->text('notes')->nullable(); //ghi chú

            $table->timestamps();

            // Chống trùng lặp
            $table->unique(['employee_id', 'shift_id', 'schedule_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
    }
};
