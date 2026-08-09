<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasilitas_tempat_tidurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->cascadeOnDelete();
            $table->foreignId('kelas_tempat_tidur_id')->constrained('kelas_tempat_tidurs')->cascadeOnDelete();
            $table->unsignedInteger('jumlah')->default(0);
            $table->timestamps();

            $table->unique(['fasilitas_id', 'kelas_tempat_tidur_id'], 'fasilitas_kelas_tempat_tidur_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas_tempat_tidurs');
    }
};
