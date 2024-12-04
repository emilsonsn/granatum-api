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
        Schema::table('whatsapp_chats', function (Blueprint $table) {
            $table->unsignedBigInteger('label_id')->after('user_id');
            $table->foreign('label_id')->references('id')->on('labels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_chats', function (Blueprint $table) {
            $table->dropForeign(['label_id']);
            $table->dropColumn(['label_id']);
        });
    }
};
