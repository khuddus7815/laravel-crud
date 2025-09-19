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
        Schema::table('items', function (Blueprint $table) {
            // Add this line to create the price column
            // We use decimal for money. (8, 2) means 8 total digits, 2 after the decimal point.
            $table->decimal('price', 8, 2)->after('description')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Add this line to tell Laravel how to remove the column
            $table->dropColumn('price');
        });
    }
};