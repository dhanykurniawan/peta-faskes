<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->string('tipe_fktp')->nullable()->after('tipe_detail');
            $table->unsignedInteger('kebutuhan_du')->default(0)->after('kantor_cabang');
            $table->unsignedInteger('peserta_terdaftar')->default(0)->after('kebutuhan_du');
            $table->unsignedInteger('prolanis_dm')->default(0)->after('peserta_terdaftar');
            $table->unsignedInteger('prolanis_ht')->default(0)->after('prolanis_dm');
            $table->unsignedInteger('peserta_prb')->default(0)->after('prolanis_ht');
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_fktp',
                'kebutuhan_du',
                'peserta_terdaftar',
                'prolanis_dm',
                'prolanis_ht',
                'peserta_prb',
            ]);
        });
    }
};
