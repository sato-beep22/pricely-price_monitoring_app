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
        Schema::table('prices', function (Blueprint $table) {
            // Tag each price with its origin: 'buyer' (manual entry) or 'admin_import' (CSV upload)
            $table->string('source')->default('buyer')->after('specification');

            // Allow null shop_id for admin-imported reference prices that don't belong to a specific shop
            $table->foreignId('shop_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->foreignId('shop_id')->nullable(false)->change();
        });
    }
};
