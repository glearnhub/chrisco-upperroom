<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->enum('category', ['general','events','youth','women','men','prayer','finance'])->default('general')->after('title');
            $table->boolean('is_pinned')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['category', 'is_pinned']);
        });
    }
};
