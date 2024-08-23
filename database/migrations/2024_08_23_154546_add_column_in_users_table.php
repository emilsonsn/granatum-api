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
        Schema::dropIfExists('collaborators');

        Schema::table('users', function (Blueprint $table) {
            $table->string('is_active')->default(1)->change();

            $table->string('phone')->nullable()->after('email');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('cpf_cnpj')->unique()->after('whatsapp');
            $table->date('birth_date')->nullable()->after('cpf_cnpj');
            $table->unsignedBigInteger('company_position_id')->nullable()->after('birth_date');
            $table->unsignedBigInteger('sector_id')->nullable()->after('company_position_id');

            $table->foreign('company_position_id')->references('id')->on('company_positions');
            $table->foreign('sector_id')->references('id')->on('sectors');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf_cnpj')->unique();
            $table->date('birth_date')->n;
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->unique();

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_position_id']);
            $table->dropForeign(['sector_id']);

            $table->dropColumn(['phone' ,'whatsapp' ,'cpf_cnpj' ,'birth_date' ,'company_position_id' ,'sector_id']);        
        });
    }
};
