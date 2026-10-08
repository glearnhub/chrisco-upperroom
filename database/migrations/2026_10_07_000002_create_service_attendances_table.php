<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('service_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('door', ['A', 'B', 'C', 'usher'])->default('A');
            $table->enum('method', ['self', 'usher'])->default('self');
            $table->timestamp('checked_in_at')->useCurrent();
            $table->timestamps();

            $table->unique(['session_id', 'user_id']); // one check-in per session
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_attendances');
    }
};
