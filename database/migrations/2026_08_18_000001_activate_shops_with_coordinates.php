<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Activate all shops that already have valid coordinates set.
     */
    public function up(): void
    {
        DB::table('shops')
            ->where('is_active', false)
            ->where(function ($query) {
                $query->where('latitude', '!=', 0)
                    ->orWhere('longitude', '!=', 0);
            })
            ->update(['is_active' => true]);
    }

    public function down(): void
    {
        // Non-reversible data migration
    }
};
