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
        Schema::create('children', function (Blueprint $table) {
            $table->id();

            // Personal
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();

            // Parent 1 (optional link to registered member + free-text fallback)
            $table->foreignId('parent1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('parent1_name')->nullable();
            $table->string('parent1_contact', 20)->nullable();

            // Parent 2 (optional)
            $table->foreignId('parent2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('parent2_name')->nullable();
            $table->string('parent2_contact', 20)->nullable();

            // Church
            $table->string('sunday_school_class', 100)->nullable();

            // Future-proofing
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
