<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function PHPUnit\Framework\once;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('fantasy_name');
            $table->string('cnpj')->unique();
            $table->string('phone');
            $table->string('whatsapp');
            $table->string('email')->unique();
            $table->unsignedBigInteger('type_supplier_id');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            
            $table->foreign('type_supplier_id')->references('id')->on('types_supplier');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
