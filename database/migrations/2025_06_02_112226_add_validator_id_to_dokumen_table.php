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
        Schema::table('dokumen', function (Blueprint $table) {
            // Tambahkan kolom validator_id jika belum ada
            if (!Schema::hasColumn('dokumen', 'validator_id')) {
                $table->unsignedBigInteger('validator_id')->nullable();
                $table->foreign('validator_id')->references('id')->on('users')->onDelete('set null');
            }
            
            // Tambahkan kolom validated_at jika belum ada
            if (!Schema::hasColumn('dokumen', 'validated_at')) {
                $table->timestamp('validated_at')->nullable();
            }
            
            // Tambahkan kolom komentar jika belum ada
            if (!Schema::hasColumn('dokumen', 'komentar')) {
                $table->text('komentar')->nullable();
            }
            
            // Tambahkan kolom deskripsi_dokumen jika belum ada
            if (!Schema::hasColumn('dokumen', 'deskripsi_dokumen')) {
                $table->text('deskripsi_dokumen')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // Hapus kolom jika ada
            if (Schema::hasColumn('dokumen', 'validator_id')) {
                $table->dropForeign(['validator_id']);
                $table->dropColumn('validator_id');
            }
            
            if (Schema::hasColumn('dokumen', 'validated_at')) {
                $table->dropColumn('validated_at');
            }
            
            if (Schema::hasColumn('dokumen', 'komentar')) {
                $table->dropColumn('komentar');
            }
            
            if (Schema::hasColumn('dokumen', 'deskripsi_dokumen')) {
                $table->dropColumn('deskripsi_dokumen');
            }
        });
    }
};
