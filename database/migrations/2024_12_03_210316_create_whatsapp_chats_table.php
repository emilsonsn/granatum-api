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
        Schema::create('whatsapp_chats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('remoteJid')->unique();                        
            $table->string('instance')->nullable();
            $table->string('instanceId');
            $table->text('profilePicUrl')->nullable();
            $table->text('apiKey')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('remoteJid');
            $table->string('externalId');
            $table->string('instanceId');
            $table->boolean('fromMe');
            $table->longText('message');
            $table->string('messageReplied')->nullable();
            $table->enum('status', ['Waiting', 'Responding', 'Finished'])->default('Waiting');
            $table->unsignedBigInteger('whatsapp_chat_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('whatsapp_chat_id')->references('id')->on('whatsapp_chats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('whatsapp_chats');
    }
};
