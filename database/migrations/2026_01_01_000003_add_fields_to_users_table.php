<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // username dipakai untuk login (selain email default Laravel)
            $table->string('username')->unique()->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('username');
            $table->boolean('is_auditor')->default(false)->after('is_active');
            $table->boolean('is_auditee')->default(false)->after('is_auditor');
            $table->string('fcm_token')->nullable()->after('is_auditee');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'is_active', 'is_auditor', 'is_auditee', 'fcm_token']);
        });
    }
};
