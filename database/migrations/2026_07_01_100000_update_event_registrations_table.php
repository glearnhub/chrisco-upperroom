<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->enum('category', ['presbyter','pastor','elder','deacon','deaconess','member','visitor'])->default('visitor')->after('email');
            $table->unsignedBigInteger('member_id')->nullable()->after('category');
            $table->foreign('member_id')->references('id')->on('users')->onDelete('set null');
            // make user_id nullable (visitors won't have accounts)
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn(['email', 'category', 'member_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
