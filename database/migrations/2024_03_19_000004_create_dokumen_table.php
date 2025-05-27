<?php
// File migrasi: 2024_03_19_000004_create_dokumen_table.php

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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->string('nama_dokumen');
            $table->text('path')->nullable();

            $ppepp_values = ['penetapan', 'pelaksanaan', 'evaluasi', 'pengendalian', 'peningkatan'];
            $table->enum('jenis_ppepp', $ppepp_values)
                  ->nullable()
                  ->comment('Tahapan PPEPP: ' . implode(', ', $ppepp_values));

            $status_values = ['draft', 'menunggu', 'revisi', 'diverifikasi'];
            $table->enum('status', $status_values)
                  ->default('draft')
                  ->comment('Status alur kerja dokumen: ' . implode(', ', $status_values));
            
            $table->boolean('is_admin_upload')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
