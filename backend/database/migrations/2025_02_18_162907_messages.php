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
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('message_id');
            $table->unsignedBigInteger('activity_id');
            $table->text('message');
            $table->tinyInteger('message_type');
            $table->boolean('delete_flag')->default(false);
            $table->timestamps();
            $table->foreign('activity_id')->references('activity_id')->on('activities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
