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
        Schema::create('child_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->date('attendance_date');
            $table->enum('method', ['face', 'manual'])->default('manual');
            $table->float('confidence')->nullable(); // face match confidence %
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->timestamps();

            $table->foreign('child_id')->references('id')->on('children')->cascadeOnDelete();
            $table->foreign('marked_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['child_id', 'attendance_date']); // one record per child per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_attendances');
    }
};
