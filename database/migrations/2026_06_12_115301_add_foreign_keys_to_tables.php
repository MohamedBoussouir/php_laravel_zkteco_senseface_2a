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
        // 1. users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
            $table->foreign('work_pattern_id')->references('id')->on('work_patterns')->onDelete('set null');
        });

        // 2. device_commands table
        Schema::table('device_commands', function (Blueprint $table) {
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });

        // 3. work_pattern_days table
        Schema::table('work_pattern_days', function (Blueprint $table) {
            $table->foreign('pattern_id')->references('id')->on('work_patterns')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
        });

        // 4. schedules table
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            // We cannot add a foreign key for user_id to users.user_id because users.user_id is not a unique index.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropForeign(['work_pattern_id']);
        });

        Schema::table('device_commands', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
        });

        Schema::table('work_pattern_days', function (Blueprint $table) {
            $table->dropForeign(['pattern_id']);
            $table->dropForeign(['shift_id']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
        });
    }
};
