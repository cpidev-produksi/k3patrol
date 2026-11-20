<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->string('finding_number')->unique();
            $table->foreignId('auditor_id')->constrained('users');
            $table->foreignId('department_id')->constrained('departments'); // PIC
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('category_other')->nullable();
            $table->text('description');
            $table->string('area_detail')->nullable();
            $table->enum('risk_level', ['Extreme', 'High', 'Medium', 'Low']);
            $table->dateTime('due_at');
            $table->enum('status', ['Open', 'On Process', 'Closed', 'Closed Verified'])->default('Open');
            $table->timestamps();

            $table->index(['status', 'due_at']);
            $table->index(['department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
