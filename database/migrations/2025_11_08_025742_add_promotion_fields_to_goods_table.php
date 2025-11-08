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
        Schema::table('goods', function (Blueprint $table) {
            $table->decimal('gia_khuyen_mai', 10, 2)->default(0)->after('gia_ban');
            $table->integer('ton_khuyen_mai')->default(0)->after('gia_khuyen_mai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn(['gia_khuyen_mai', 'ton_khuyen_mai']);
        });
    }
};
