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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('residence')->nullable();
            $table->string('occupation')->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed', 'Separated'])->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->json('preferred_contact')->nullable();
            $table->boolean('visited_before')->default(false);
            $table->boolean('is_chrisco_member')->default(false);
            $table->string('chrisco_church')->nullable();
            $table->boolean('attends_another_church')->default(false);
            $table->string('another_church_name')->nullable();
            $table->date('visit_date');
            $table->string('invited_by')->nullable();
            $table->enum('how_heard', ['Friend', 'Family', 'Social Media', 'Website', 'Walk In', 'Evangelism', 'Other'])->nullable();
            $table->text('prayer_request')->nullable();
            $table->enum('follow_up_status', ['pending', 'contacted', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
