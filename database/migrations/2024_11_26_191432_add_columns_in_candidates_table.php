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
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('cep')->after('phone');
            $table->string('state')->after('cep');
            $table->string('city')->after('state');
            $table->string('neighborhood')->after('city');
            $table->string('street')->after('neighborhood');
            $table->string('number')->after('street');
            $table->string('marital_status')->after('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('cep');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('neighborhood');
            $table->dropColumn('street');
            $table->dropColumn('number');
            $table->dropColumn('marital_status');
        });
    }
};
