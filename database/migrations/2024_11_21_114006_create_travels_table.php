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
        Schema::create('travels', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('type');
            $table->string('transport');
            $table->date('purchase_date')->nullable();
            $table->decimal('total_value');
            $table->boolean('has_granatum')->default(false);
            $table->enum('purchase_status', ['Pending', 'Resolved', 'RequestFinance', 'RequestManager'])->default('Pending');
            $table->string('observations')->nulalble();
            $table->integer('bank_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travels');
    }
};
