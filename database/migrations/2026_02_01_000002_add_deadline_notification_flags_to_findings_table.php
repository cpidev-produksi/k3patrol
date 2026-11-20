<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            // Mencegah notifikasi terkirim berulang-ulang untuk temuan yang sama.
            $table->dateTime('near_deadline_notified_at')->nullable()->after('due_at');
            $table->dateTime('overdue_notified_at')->nullable()->after('near_deadline_notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('findings', function (Blueprint $table) {
            $table->dropColumn(['near_deadline_notified_at', 'overdue_notified_at']);
        });
    }
};
