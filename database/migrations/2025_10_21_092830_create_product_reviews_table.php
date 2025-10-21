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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->string('product_type');
            $table->tinyInteger('rating')
                ->unsigned()
                ->comment('Điểm đánh giá từ 1-5'); // 1-5 sao
            $table->text('comment')
                ->nullable()
                ->comment('Nội dung bình luận');
            
            // Admin moderation (để tránh spam/nội dung xấu)
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('approved') // Mặc định approved, không cần duyệt
                ->comment('Trạng thái duyệt');
            
            $table->timestamps();
            
            // Indexes để tăng tốc query
            $table->index(['product_id', 'product_type']);
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
