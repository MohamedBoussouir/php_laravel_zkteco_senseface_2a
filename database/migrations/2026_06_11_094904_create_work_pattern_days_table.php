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
        Schema::create('work_pattern_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pattern_id')->nullable();
            $table->integer('day_of_week'); // 0 (Sunday) to 6 (Saturday)
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->timestamps();

            $table->unique(['pattern_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_pattern_days');
    }
};
