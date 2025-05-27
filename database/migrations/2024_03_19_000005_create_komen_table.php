<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_id')->nullable()->constrained('dokumen')->onDelete('cascade');
            $table->foreignId('kriteria_id')->nullable()->constrained('kriteria')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('komentar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komen');
    }
};
