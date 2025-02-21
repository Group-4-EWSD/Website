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
        Schema::create('article_details', function (Blueprint $table) {
            $table->string('article_detail_id', 10)->primary();
            $table->string('article_id', 10);
            $table->string('file_path', 255);
            $table->string('file_name', 255);
            $table->string('file_type', 50);
            $table->foreign('article_id')->references('article_id')->on('articles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_details');
    }
};
