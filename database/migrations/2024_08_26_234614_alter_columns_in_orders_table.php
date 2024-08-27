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
            $table->enum('purchase_status', ['Pending', 'Resolved', 'RequestFinance'])->change();
            $table->date('purchase_date')->nullable()->after('purchase_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('purchase_status', ['Resolved', 'Request Finance'])->change();
            $table->dropColumn('purchase_date');
        });
    }
};
