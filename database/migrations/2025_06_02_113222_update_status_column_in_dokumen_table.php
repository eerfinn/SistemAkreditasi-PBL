<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Karena MySQL tidak memungkinkan modifikasi ENUM secara langsung,
        // kita perlu mengubah tipe kolom menjadi string terlebih dahulu
        
        // 1. Ubah tipe kolom dari ENUM menjadi VARCHAR
        Schema::table('dokumen', function (Blueprint $table) {
            // Ubah kolom status menjadi VARCHAR
            DB::statement("ALTER TABLE dokumen MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'draft'");
        });
        
        // 2. Ubah kembali menjadi ENUM dengan nilai yang diperbarui
        Schema::table('dokumen', function (Blueprint $table) {
            $status_values = ['draft', 'menunggu', 'revisi', 'diverifikasi', 'menunggu_direktur'];
            DB::statement("ALTER TABLE dokumen MODIFY COLUMN status ENUM('" . implode("','", $status_values) . "') NOT NULL DEFAULT 'draft'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke definisi ENUM asli
        Schema::table('dokumen', function (Blueprint $table) {
            $status_values = ['draft', 'menunggu', 'revisi', 'diverifikasi'];
            DB::statement("ALTER TABLE dokumen MODIFY COLUMN status ENUM('" . implode("','", $status_values) . "') NOT NULL DEFAULT 'draft'");
        });
    }
};
