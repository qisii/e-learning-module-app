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
        Schema::create('handout_score', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('handout_id');
            $table->unsignedBigInteger('score');
            $table->timestamps();

            $table->foreign('handout_id')->references('id')->on('handouts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handout_score');
    }
};
