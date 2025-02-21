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
            $table->string('article_id', 10)->primary();
            $table->string('article_title', 255);
            $table->text('article_description');
            $table->tinyInteger('article_status');
            $table->string('user_id', 10);
            $table->string('approver_id', 10)->nullable();
            $table->string('article_type_id', 10);
            $table->boolean('delete_flag')->default(false);
            $table->timestamps();
            $table->foreign('article_type_id')->references('article_type_id')->on('article_types');
            $table->foreign('user_id')->references('user_id')->on('users');
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
