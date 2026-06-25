<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
    public function up(): void
    {
        // Nullify device_id for attendances where the device no longer exists
        \Illuminate\Support\Facades\DB::table('attendances')
            ->whereNotIn('device_id', function($query) {
                $query->select('id')->from('devices');
            })
            ->update(['device_id' => null]);

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
        });

        // Nullify shift_id for monthly_pattern_days where the shift no longer exists
        \Illuminate\Support\Facades\DB::table('monthly_pattern_days')
            ->whereNotNull('shift_id')
            ->whereNotIn('shift_id', function($query) {
                $query->select('id')->from('shifts');
            })
            ->update(['shift_id' => null]);

        Schema::table('monthly_pattern_days', function (Blueprint $table) {
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
        });

        Schema::table('monthly_pattern_days', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
        });
    }
};
