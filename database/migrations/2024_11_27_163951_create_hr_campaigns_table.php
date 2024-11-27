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
        Schema::create('hr_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['Single','Recurrence']);
            $table->enum('recurrence_type', ['Monthly', 'Fortnightly', 'Weekly'])->nullable();
            $table->unsignedBigInteger('selection_process_id');
            $table->unsignedBigInteger('status_id')->nullable();
            $table->enum('channels', ['Email', 'Whatsapp']);
            $table->dateTime('start_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('selection_process_id')->references('id')->on('selection_process');
            $table->foreign('status_id')->references('id')->on('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_campaigns');
    }
};
