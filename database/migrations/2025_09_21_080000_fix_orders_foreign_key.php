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
            // Drop the old foreign key constraint
            $table->dropForeign('orders_customer_id_foreign');

            // Add the new foreign key constraint
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['user_id']);

            // Re-add the old foreign key constraint
            $table->foreignId('user_id')->nullable()->constrained('customers')->onDelete('set null')->foreign('orders_customer_id_foreign');
        });
    }
};
