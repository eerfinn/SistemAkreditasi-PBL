<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->string('nama_dokumen');
            $table->text('path');
            $table->enum('status', ['menunggu', 'revisi', 'diterima', 'diverifikasi'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
}; 