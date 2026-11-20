<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finding_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_id')->constrained('findings')->cascadeOnDelete();
            $table->enum('type', ['temuan', 'perbaikan']);
            $table->string('path');
            $table->dateTime('taken_at')->nullable();
            $table->string('file_hash', 64); // sha256
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();

            $table->unique('file_hash'); // cegah upload foto yang sama 2x (global)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finding_photos');
    }
};
