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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('whatsapp_chats', function (Blueprint $table) {
            $table->enum('status', ['Waiting', 'Responding', 'Finished'])
                ->default('Waiting')
                ->after('profilePicUrl');        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_chats', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->enum('status', ['Waiting', 'Responding', 'Finished'])->default('Waiting');        
        });
    }
};
