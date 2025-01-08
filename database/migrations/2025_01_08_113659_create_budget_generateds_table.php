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
        Schema::create('budget_generateds', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->unsignedBigInteger('budget_id');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->enum('status', ['Generated', 'Delivered', 'Approved', 'Desapproved'])
                ->default('Generated');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('budget_id')
                ->references('id')
                ->on('budgets');

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads');
        });

        Schema::create('budget_variables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_generated_id');
            $table->string('key');
            $table->string('value');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('budget_generated_id')
                ->references('id')
                ->on('budget_generateds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_variables');
        Schema::dropIfExists('budget_generateds');
    }
};
