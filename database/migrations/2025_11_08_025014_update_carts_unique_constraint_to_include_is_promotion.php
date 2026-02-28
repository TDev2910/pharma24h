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
        Schema::table('carts', function (Blueprint $table) {
            // Xóa foreign key trước (nếu có)
            $table->dropForeign(['user_id']);
            $table->dropUnique('carts_user_id_session_id_item_id_item_type_unique');
        });
        
        Schema::table('carts', function (Blueprint $table) {
            // Thêm unique constraint mới bao gồm is_promotion
            // Cho phép cùng một sản phẩm có cả bản khuyến mãi và không khuyến mãi
            $table->unique(['user_id', 'session_id', 'item_id', 'item_type', 'is_promotion'], 'carts_unique');
            
            // Thêm lại foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Xóa foreign key trước
            $table->dropForeign(['user_id']);
            $table->dropUnique('carts_unique');
        });
        
        Schema::table('carts', function (Blueprint $table) {
            // Khôi phục unique constraint cũ
            $table->unique(['user_id', 'session_id', 'item_id', 'item_type']);
            
            // Thêm lại foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
