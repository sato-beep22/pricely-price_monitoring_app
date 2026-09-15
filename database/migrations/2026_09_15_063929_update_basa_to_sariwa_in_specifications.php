<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update crops table
        DB::statement("UPDATE crops SET specification = REPLACE(specification, 'basa', 'sariwa')");
        
        // Update prices table
        DB::statement("UPDATE prices SET specification = REPLACE(specification, 'basa', 'sariwa')");
        
        // Update ceiling_prices table
        DB::statement("UPDATE ceiling_prices SET specification = REPLACE(specification, 'basa', 'sariwa')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert crops table
        DB::statement("UPDATE crops SET specification = REPLACE(specification, 'sariwa', 'basa')");
        
        // Revert prices table
        DB::statement("UPDATE prices SET specification = REPLACE(specification, 'sariwa', 'basa')");
        
        // Revert ceiling_prices table
        DB::statement("UPDATE ceiling_prices SET specification = REPLACE(specification, 'sariwa', 'basa')");
    }
};
