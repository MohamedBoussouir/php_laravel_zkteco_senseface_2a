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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->string('user_id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('role')->default(0);
            $table->unsignedBigInteger('device_id')->nullable();
            $table->text('fingerprints')->nullable();
            $table->unsignedBigInteger('work_pattern_id')->nullable();
            $table->longText('photo')->nullable();
            $table->string('password')->nullable();
            $table->string('card_number')->nullable();
            $table->boolean('has_face')->default(false);
            $table->bigInteger('last_sync_time')->default(0);
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['user_id', 'device_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
