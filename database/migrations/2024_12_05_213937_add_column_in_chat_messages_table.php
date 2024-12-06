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
            $table->enum('type', ['Text', 'Audio', 'File'])
                ->default('Text')
                ->after('unread');

            $table->string('path')
                ->nullable()
                ->after('type');
            
            $table->longText('message')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('path');
            $table->longText('message')                
                ->change();            
        });
    }
};
