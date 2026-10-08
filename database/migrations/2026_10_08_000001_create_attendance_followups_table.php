<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->enum('reason', [
                'transferred',
                'left_church',
                'unwell',
                'job_related',
                'mission_field',
                'other',
            ]);
            $table->string('transferred_to')->nullable(); // church name when reason = transferred
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'year', 'month']); // one follow-up record per member per month
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_followups');
    }
};
