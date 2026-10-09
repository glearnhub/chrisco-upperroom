<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_medical_condition')->default(false)->after('deacon_name');
            $table->text('medical_conditions')->nullable()->after('has_medical_condition');
            $table->text('medications')->nullable()->after('medical_conditions');
            $table->string('allergies')->nullable()->after('medications');
            $table->string('emergency_medical_contact')->nullable()->after('allergies');
            $table->string('emergency_medical_phone')->nullable()->after('emergency_medical_contact');
            $table->text('special_needs')->nullable()->after('emergency_medical_phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'has_medical_condition', 'medical_conditions', 'medications',
                'allergies', 'emergency_medical_contact', 'emergency_medical_phone', 'special_needs',
            ]);
        });
    }
};
