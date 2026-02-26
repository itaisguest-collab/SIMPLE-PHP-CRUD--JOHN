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
        // make timestamps nullable to avoid NOT NULL constraint on inserts
        DB::statement("ALTER TABLE `employees` MODIFY `created_at` TIMESTAMP NULL, MODIFY `updated_at` TIMESTAMP NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `employees` MODIFY `created_at` TIMESTAMP NOT NULL, MODIFY `updated_at` TIMESTAMP NOT NULL");
    }
};
