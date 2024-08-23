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
        Schema::table('collaborators', function (Blueprint $table) {
            $table->unsignedBigInteger('company_position_id')->after('birth_date');
            $table->unsignedBigInteger('sector_id')->after('company_position_id');

            $table->foreign('company_position_id')->references('id')->on('company_positions');
            $table->foreign('sector_id')->references('id')->on('sectors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborators', function (Blueprint $table) {
            $table->dropForeign(['company_position_id']);
            $table->dropColumn(['sector_id']);

            $table->dropColumn('company_position_id');
            $table->dropColumn('sector_id');
        });
    }
};
