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
        Schema::create('pdf_resources', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('folder_id');
            $table->unsignedBigInteger('handout_id')->nullable();
            $table->unsignedBigInteger('quiz_id')->nullable();

            $table->string('title')->nullable();
            $table->text('gdrive_link'); // stored as a string
            $table->timestamps();

            // Foreign keys
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('handout_id')->references('id')->on('handouts')->onDelete('cascade');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_resources');
    }
};
