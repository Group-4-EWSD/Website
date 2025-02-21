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
        Schema::create('system_datas', function (Blueprint $table) {
            $table->string('system_id', 10)->primary();
            $table->string('system_title', 255);
            $table->date('pre_submission_date');
            $table->tinyInteger('system_status');
            $table->string('creator_id', 10);
            $table->string('category_id', 10);
            $table->string('academic_year_id', 10);
            $table->boolean('delete_flag')->default(false);
            $table->timestamps();
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_datas');
    }
};
