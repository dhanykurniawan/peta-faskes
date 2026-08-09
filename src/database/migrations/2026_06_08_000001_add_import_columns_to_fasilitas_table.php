<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->string('kode_faskes')->nullable()->after('kelas');
            $table->string('kantor_cabang')->nullable()->after('kode_faskes');
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropColumn(['kode_faskes', 'kantor_cabang']);
        });
    }
};
