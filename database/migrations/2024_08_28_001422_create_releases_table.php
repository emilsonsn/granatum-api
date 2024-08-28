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
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('release_id');
            $table->bigInteger('category_id');
            $table->bigInteger('account_bank_id');
            $table->text('description');
            $table->decimal('value');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->longText('api_response')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
