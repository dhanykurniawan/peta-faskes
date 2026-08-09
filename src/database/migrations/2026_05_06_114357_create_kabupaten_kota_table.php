<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kabupaten_kota', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode_bps')->unique()->nullable();
            $table->bigInteger('populasi')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabupaten_kota');
    }
};
