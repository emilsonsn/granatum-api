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
        Schema::table('constructions', function (Blueprint $table) {
            $table->string('cno')->change();
        
            // Remova as chaves estrangeiras uma por uma com o nome correto.
            $table->dropForeign(['contractor_id']);
            $table->dropForeign(['client_id']);
        
            // Adicione as novas chaves estrangeiras.
            $table->foreign('contractor_id')->references('id')->on('clients');
            $table->foreign('client_id')->references('id')->on('clients');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constructions', function (Blueprint $table) {
            $table->integer('cno')->change();
        
            $table->dropForeign(['contractor_id']);
            $table->dropForeign(['client_id']);
        
            $table->foreign('contractor_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('users');
        });
        
    }
};
