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
        Schema::table('komen', function (Blueprint $table) {
            // Make dokumen_id nullable since comments can be for either dokumen or kriteria
            $table->foreignId('dokumen_id')->nullable()->change();
            
            // Add kriteria_id column
            $table->foreignId('kriteria_id')->nullable()->after('dokumen_id')->constrained('kriteria')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('komen', function (Blueprint $table) {
            $table->dropForeign(['kriteria_id']);
            $table->dropColumn('kriteria_id');
            $table->foreignId('dokumen_id')->nullable(false)->change();
        });
    }
};
