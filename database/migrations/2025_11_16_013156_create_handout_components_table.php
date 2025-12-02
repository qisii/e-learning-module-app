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
        Schema::create('handout_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('type');
            $table->json('data')->nullable();    // Tiptap JSON or other component metadata
            $table->integer('sort_order')->default(0);   // used by SortableJS
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('handout_pages')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handout_components');
    }
};
