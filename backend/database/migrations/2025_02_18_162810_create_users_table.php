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
            $table->string('user_id', 10)->primary();
            $table->string('user_name', 255);
            $table->string('nickname', 100)->nullable();
            $table->string('user_email', 255)->unique();
            $table->string('user_password', 255);
            $table->uuid('user_type_id');
            $table->uuid('faculty_id')->nullable();
            $table->tinyInteger('gender');
            $table->string('user_photo_path', 255)->nullable();
            $table->boolean('delete_flag')->default(false);
            $table->timestamps();
            $table->foreign('user_type_id')->references('user_type_id')->on('user_types');
            $table->foreign('faculty_id')->references('faculty_id')->on('faculties');
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
