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
        Schema::create('budget_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_id');
            $table->longText('presentation_text_1')->nullable();
            $table->longText('presentation_text_2')->nullable();
            $table->longText('presentation_text_3')->nullable();
            $table->longText('development_text_1')->nullable();
            $table->longText('development_text_2')->nullable();
            $table->longText('development_text_3')->nullable();
            $table->longText('development_text_4')->nullable();
            $table->longText('payment_methods')->nullable();
            $table->longText('conclusion_text_1')->nullable();
            $table->longText('conclusion_text_2')->nullable();
            $table->string('presentation_image_1')->nullable();
            $table->string('presentation_image_2')->nullable();
            $table->string('presentation_image_3')->nullable();
            $table->string('development_image_1')->nullable();
            $table->string('development_image_2')->nullable();
            $table->string('development_image_3')->nullable();
            $table->string('development_image_4')->nullable();
            $table->string('conclusion_image_1')->nullable();
            $table->string('conclusion_image_2')->nullable();
            $table->string('cover')->nullable();
            $table->string('final_cover')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('budget_id')->references('id')->on('budgets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_details');
    }
};
