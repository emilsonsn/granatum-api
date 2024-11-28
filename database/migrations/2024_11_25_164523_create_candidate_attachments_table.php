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
        Schema::create('candidate_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->unsignedBigInteger('candidate_id');
            $table->unsignedBigInteger('selection_process_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('candidate_id')->references('id')->on('candidates');
            $table->foreign('selection_process_id')->references('id')->on('selection_process');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_attachments');
    }
};
