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
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->unsignedBigInteger('profession_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('profession_id')->references('id')->on('professions');
        });

        Schema::create('selection_process', function (Blueprint $table) {
            $table->id();
            $table->integer('total_candidates')->nullable();
            $table->integer('available_vacancies');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vacancy_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vacancy_id')->references('id')->on('vacancies');
        });

        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('color');
            $table->unsignedBigInteger('selection_process_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('selection_process_id')->references('id')->on('selection_process');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_process_status');
        Schema::dropIfExists('selection_process');
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('professions');
    }
};
