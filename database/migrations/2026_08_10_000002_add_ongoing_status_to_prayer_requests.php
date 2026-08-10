<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE prayer_requests MODIFY COLUMN status ENUM('ongoing','pending','prayed','answered') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE prayer_requests MODIFY COLUMN status ENUM('pending','prayed','answered') NOT NULL DEFAULT 'pending'");
    }
};
