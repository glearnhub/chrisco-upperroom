<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_born_again')->default(false)->after('salvation_date');
            $table->boolean('is_baptized')->default(false)->after('is_born_again');
            $table->string('baptism_date')->nullable()->after('is_baptized');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_born_again', 'is_baptized', 'baptism_date']);
        });
    }
};
