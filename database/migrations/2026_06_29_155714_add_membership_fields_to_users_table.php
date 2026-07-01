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
            $table->string('middle_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->enum('gender', ['male', 'female'])->nullable()->after('last_name');
            $table->string('county')->nullable()->after('address');
            $table->string('sub_county')->nullable()->after('county');
            $table->string('sub_location')->nullable()->after('sub_county');
            $table->string('salvation_date')->nullable()->after('sub_location');
            $table->string('committed_date')->nullable()->after('salvation_date');
            $table->string('department')->nullable()->after('committed_date');
            $table->string('occupation')->nullable()->after('department');
            $table->string('next_of_kin_name')->nullable()->after('occupation');
            $table->string('next_of_kin_relationship')->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_phone')->nullable()->after('next_of_kin_relationship');
            $table->string('home_cell')->nullable()->after('next_of_kin_phone');
            $table->string('deacon_name')->nullable()->after('home_cell');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name','last_name','gender','county','sub_county','sub_location',
                'salvation_date','committed_date','department','occupation',
                'next_of_kin_name','next_of_kin_relationship','next_of_kin_phone',
                'home_cell','deacon_name',
            ]);
        });
    }
};
