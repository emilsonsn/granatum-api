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
            // $table->integer('tag_id')->nullable()->after('category_id');
            $table->integer('external_suplier_id')->nullable()->after('tag_id',);
        });

        Schema::table('travels', function (Blueprint $table) {
            $table->integer('tag_id')->nullable()->after('category_id');
            $table->integer('external_suplier_id')->nullable()->after('tag_id',);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('tag_id');
            $table->dropColumn('external_suplier_id');
        });

        Schema::table('travels', function (Blueprint $table) {
            $table->dropColumn('tag_id');
            $table->dropColumn('external_suplier_id');
        });
    }
};
