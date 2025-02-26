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
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('article_id')->primary();
            $table->string('article_title', 255);
            $table->text('article_description');
            $table->uuid('user_id');
            $table->uuid('system_id');
            $table->string('approver_id', 10)->nullable();
            $table->uuid('article_type_id');
            $table->boolean('delete_flag')->default(false);
            $table->timestamps();
            $table->foreign('article_type_id')->references('article_type_id')->on('article_types');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('system_id')->references('system_id')->on('system_datas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
