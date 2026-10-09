<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'in' = transferred in from another Chrisco church
            // 'out' = transferred out to another Chrisco church
            // null  = regular / not transferred
            $table->string('transfer_type')->nullable()->after('membership_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('transfer_type');
        });
    }
};
