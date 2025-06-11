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
            // Remove unused columns
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // Add columns back if migration is rolled back
            $table->timestamp('validated_at')->nullable();
            $table->text('komentar')->nullable();
            $table->text('deskripsi_dokumen')->nullable();
        });
    }
};
