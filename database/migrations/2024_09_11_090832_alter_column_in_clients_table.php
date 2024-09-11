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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('cpf_cnpj')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('whatsapp')->nullable()->change();
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('cpf_cnpj')->unique()->change();
            $table->string('phone')->change();
            $table->string('whatsapp')->change();
            $table->string('email')->unique()->change();
        });
    }
};
