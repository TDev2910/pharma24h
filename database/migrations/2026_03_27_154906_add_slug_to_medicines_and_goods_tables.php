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
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('ten_thuoc');
        });
        Schema::table('goods', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('ten_hang_hoa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines_and_goods_tables', function (Blueprint $table) {
            //
        });
    }
};
