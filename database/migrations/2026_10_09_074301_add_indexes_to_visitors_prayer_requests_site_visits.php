<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->index('follow_up_status', 'idx_visitors_followup_status');
        });

        Schema::table('prayer_requests', function (Blueprint $table) {
            $table->index('status', 'idx_prayer_requests_status');
        });

        Schema::table('site_visits', function (Blueprint $table) {
            $table->index(['ip', 'created_at'], 'idx_site_visits_ip_created');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex('idx_visitors_followup_status');
        });

        Schema::table('prayer_requests', function (Blueprint $table) {
            $table->dropIndex('idx_prayer_requests_status');
        });

        Schema::table('site_visits', function (Blueprint $table) {
            $table->dropIndex('idx_site_visits_ip_created');
        });
    }
};
