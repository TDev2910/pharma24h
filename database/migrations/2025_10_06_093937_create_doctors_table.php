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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('doctor_code',6)->unique();
            $table->string('name');;
            $table->enum('gender',['Male','Female']);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->string('province_district', 100)->nullable()->comment('Province - District'); // Like suppliers
            $table->string('ward_commune', 100)->nullable()->comment('Ward/Commune'); // Like suppliers
            $table->string('specialty')->nullable(); // Specialty (direct storage)
            $table->string('qualification')->nullable(); // Qualification (direct storage)
            $table->text('note')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
