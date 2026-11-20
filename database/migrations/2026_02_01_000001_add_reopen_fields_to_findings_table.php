<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->text('reopen_note')->nullable()->after('status');
            $table->dateTime('reopened_at')->nullable()->after('reopen_note');
            $table->foreignId('reopened_by')->nullable()->after('reopened_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reopened_by');
            $table->dropColumn(['reopen_note', 'reopened_at']);
        });
    }
};
