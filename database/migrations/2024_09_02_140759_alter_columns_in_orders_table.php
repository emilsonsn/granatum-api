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
            $table->enum('purchase_status', ['Pending', 'Resolved', 'RequestFinance'])->default('Pending')->change();
        });

        Schema::table('solicitations', function (Blueprint $table) {
            $table->enum('status', ['Finished', 'Pending', 'Rejected'])->default('Pending')->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->date('concluded_at')->nullable()->change();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('purchase_status', ['Pending', 'Resolved', 'RequestFinance'])->change();
        });

        Schema::table('solicitations', function (Blueprint $table) {
            $table->enum('status', ['Finished', 'Pending', 'Rejected'])->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->date('concluded_at')->change();
        });
    }
};
