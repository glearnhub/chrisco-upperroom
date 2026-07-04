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
        Schema::table('site_visits', function (Blueprint $table) {
            $table->string('country', 100)->nullable()->after('session_id');
            $table->string('country_code', 5)->nullable()->after('country');
            $table->boolean('is_new_session')->default(true)->after('country_code');
        });
    }

    public function down(): void
    {
        Schema::table('site_visits', function (Blueprint $table) {
            $table->dropColumn(['country', 'country_code', 'is_new_session']);
        });
    }
};
