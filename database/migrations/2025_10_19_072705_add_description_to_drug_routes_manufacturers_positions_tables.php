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
        // Add description column to drug_routes table
        Schema::table('drug_routes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        // Add description column to manufacturers table
        Schema::table('manufacturers', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        // Add description column to positions table
        Schema::table('positions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove description column from drug_routes table
        Schema::table('drug_routes', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        // Remove description column from manufacturers table
        Schema::table('manufacturers', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        // Remove description column from positions table
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};