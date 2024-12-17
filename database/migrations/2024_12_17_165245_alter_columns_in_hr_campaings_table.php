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
        Schema::table('hr_campaigns', function (Blueprint $table) {
            $table->string('channels')->change();
            $table->time('start_time')->after('start_date');
            $table->boolean('is_active')->default(true)->after('start_time');
        });

        Schema::table('crm_campaigns', function (Blueprint $table) {
            $table->string('channels')->change();
            $table->time('start_time')->after('start_date');
            $table->boolean('is_active')->default(true)->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_campaigns', function (Blueprint $table) {
            $table->enum('channels', ['Whatsapp', 'Email'])->change();
            $table->dropColumn('start_time');
            $table->dropColumn('is_active');
        });

        Schema::table('crm_campaigns', function (Blueprint $table) {
            $table->enum('channels', ['Whatsapp', 'Email'])->change();
            $table->dropColumn('start_time');
            $table->dropColumn('is_active');
        });
    }
};
