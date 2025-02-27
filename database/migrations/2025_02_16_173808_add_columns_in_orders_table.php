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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('cost_center_id')
                ->after('category_id');
        });

        Schema::table('travels', function (Blueprint $table) {
            $table->integer('cost_center_id')
                ->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('cost_center_id');                
        });

        Schema::table('travels', function (Blueprint $table) {
            $table->dropColumn('cost_center_id');
        });
    }
};
