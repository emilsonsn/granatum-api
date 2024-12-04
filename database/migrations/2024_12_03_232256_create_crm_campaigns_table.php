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
        Schema::create('crm_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['Single','Recurrence']);
            $table->enum('recurrence_type', ['Monthly', 'Fortnightly', 'Weekly'])->nullable();
            $table->unsignedBigInteger('funnel_id');
            $table->unsignedBigInteger('funnel_step_id')->nullable();
            $table->enum('channels', ['Email', 'Whatsapp']);
            $table->dateTime('start_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('funnel_id')
                ->references('id')
                ->on('funnels');
                
            $table->foreign('funnel_step_id')
                ->references('id')
                ->on('funnel_steps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_campaigns');
    }
};
