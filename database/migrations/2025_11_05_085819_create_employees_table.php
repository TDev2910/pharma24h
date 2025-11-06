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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Liên kết với users
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Thông tin cơ bản
            $table->string('full_name');
            $table->string('phone_number', 20)->unique()->nullable();
            $table->string('employee_code', 50)->unique()->nullable();

            // Thông tin lương
            $table->enum('salary_type', ['fixed', 'per_hour'])->default('fixed');
            $table->decimal('salary_level', 15, 2)->default(0);

            // Thông tin công việc
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->onDelete('set null');

            $table->foreignId('job_title_id')
                ->nullable()
                ->constrained('job_titles')
                ->onDelete('set null');

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->onDelete('set null');

            $table->date('start_date')->nullable();

            // Thông tin cá nhân
            $table->date('dob')->nullable();
            $table->enum('gender', ['nam', 'nữ'])->nullable();
            $table->text('address')->nullable();
            $table->string('id_card_number', 25)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
