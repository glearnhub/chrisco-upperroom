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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('belongs_to_home_cell')->default(false)->after('next_of_kin_phone');
            $table->boolean('assigned_to_deacon')->default(false)->after('home_cell');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['belongs_to_home_cell', 'assigned_to_deacon']);
        });
    }
};
