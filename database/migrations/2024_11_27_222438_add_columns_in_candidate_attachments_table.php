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
        Schema::table('candidate_attachments', function (Blueprint $table) {
            if (!Schema::hasColumn('candidate_attachments', 'selection_process_id')) {
                $table->unsignedBigInteger('selection_process_id')->nullable()->after('candidate_id');
                $table->foreign('selection_process_id')
                    ->references('id')
                    ->on('selection_process');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_attachments', function (Blueprint $table) {
            if (Schema::hasColumn('candidate_attachments', 'selection_process_id')) {
                $table->dropForeign(['selection_process_id']);
                $table->dropColumn(['selection_process_id']);
            }
        });
    }
};
