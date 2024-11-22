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
        Schema::table('releases', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->change();
            $table->unsignedBigInteger('travel_id')->nullable()->after('order_id');
            $table->foreign('travel_id')->references('id')->on('travels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('releases', function (Blueprint $table) {
            $table->dropForeign(['travel_id']);
            $table->dropColumn('travel_id');
            $table->unsignedBigInteger('order_id')->nullable();            
        });
    }
};
