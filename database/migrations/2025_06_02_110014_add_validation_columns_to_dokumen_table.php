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
            // Tambahkan kolom jika belum ada
            if (!Schema::hasColumn('dokumen', 'koordinator_id')) {
                $table->unsignedBigInteger('koordinator_id')->nullable();
                $table->foreign('koordinator_id')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('dokumen', 'direktur_id')) {
                $table->unsignedBigInteger('direktur_id')->nullable();
                $table->foreign('direktur_id')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('dokumen', 'validator_level')) {
                $table->string('validator_level')->nullable();
            }
            
            if (!Schema::hasColumn('dokumen', 'koordinator_validated_at')) {
                $table->timestamp('koordinator_validated_at')->nullable();
            }
            
            if (!Schema::hasColumn('dokumen', 'direktur_validated_at')) {
                $table->timestamp('direktur_validated_at')->nullable();
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
            if (Schema::hasColumn('dokumen', 'koordinator_id')) {
                $table->dropForeign(['koordinator_id']);
                $table->dropColumn('koordinator_id');
            }
            
            if (Schema::hasColumn('dokumen', 'direktur_id')) {
                $table->dropForeign(['direktur_id']);
                $table->dropColumn('direktur_id');
            }
            
            if (Schema::hasColumn('dokumen', 'validator_level')) {
                $table->dropColumn('validator_level');
            }
            
            if (Schema::hasColumn('dokumen', 'koordinator_validated_at')) {
                $table->dropColumn('koordinator_validated_at');
            }
            
            if (Schema::hasColumn('dokumen', 'direktur_validated_at')) {
                $table->dropColumn('direktur_validated_at');
            }
        });
    }
};
