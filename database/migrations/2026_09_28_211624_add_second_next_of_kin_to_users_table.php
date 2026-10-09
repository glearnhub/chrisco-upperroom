<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('next_of_kin2_name')->nullable()->after('next_of_kin_phone');
            $table->string('next_of_kin2_relationship')->nullable()->after('next_of_kin2_name');
            $table->string('next_of_kin2_phone')->nullable()->after('next_of_kin2_relationship');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['next_of_kin2_name', 'next_of_kin2_relationship', 'next_of_kin2_phone']);
        });
    }
};
