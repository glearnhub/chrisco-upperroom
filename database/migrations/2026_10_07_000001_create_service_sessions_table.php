<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('service_date');
            $table->enum('service_type', ['sunday_morning', 'sunday_afternoon', 'special'])->default('sunday_morning');
            $table->string('label')->nullable(); // e.g. "1st Service", "Youth Sunday"
            $table->enum('status', ['scheduled', 'open', 'closed'])->default('scheduled');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['service_date', 'service_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_sessions');
    }
};
