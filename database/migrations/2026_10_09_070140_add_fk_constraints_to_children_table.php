<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullify any parent IDs that point to deleted users before adding constraints
        \DB::statement('UPDATE children SET parent1_id = NULL WHERE parent1_id IS NOT NULL AND parent1_id NOT IN (SELECT id FROM users)');
        \DB::statement('UPDATE children SET parent2_id = NULL WHERE parent2_id IS NOT NULL AND parent2_id NOT IN (SELECT id FROM users)');

        Schema::table('children', function (Blueprint $table) {
            $table->foreign('parent1_id', 'fk_children_parent1')->references('id')->on('users')->nullOnDelete();
            $table->foreign('parent2_id', 'fk_children_parent2')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign('fk_children_parent1');
            $table->dropForeign('fk_children_parent2');
        });
    }
};
