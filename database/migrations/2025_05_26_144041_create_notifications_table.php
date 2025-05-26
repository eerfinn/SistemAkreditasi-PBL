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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable(); // dokumen, komen, validasi, etc
            $table->unsignedBigInteger('dokumen_id')->nullable();
            $table->foreign('dokumen_id')->references('id')->on('dokumen')->onDelete('cascade');
            $table->foreignId('kriteria_id')->nullable()->references('id')->on('kriteria')->onDelete('cascade');
            $table->string('icon')->default('fa-bell');
            $table->string('color')->default('primary'); // primary, success, warning, danger
            $table->boolean('is_read')->default(false);
            $table->string('link')->nullable(); // URL untuk diarahkan ketika notifikasi diklik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
