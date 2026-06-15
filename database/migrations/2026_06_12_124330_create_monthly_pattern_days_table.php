<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_pattern_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_pattern_id')->constrained()->cascadeOnDelete();
            $table->integer('day_of_month'); // 1 to 31
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_pattern_days');
    }
};
