<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE church_leaders MODIFY COLUMN role VARCHAR(150) NOT NULL DEFAULT 'other'");
    }

    public function down(): void
    {
        DB::statement("UPDATE church_leaders SET role = 'other' WHERE role NOT IN ('founder','bishop','pastor','other')");
        DB::statement("ALTER TABLE church_leaders MODIFY COLUMN role ENUM('founder','bishop','pastor','other') NOT NULL DEFAULT 'other'");
    }
};
