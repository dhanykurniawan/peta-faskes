<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public const LAYANAN = [
        'HD',
        'Kemoterapi',
        'Radioterapi',
        'Cathlab',
        'Labor PK',
        'Labor PA',
        'Rongent',
        'Ro Panoramic',
        'Echocardiografi',
        'ESWL',
        'Fakoemulsifikasi Set',
        'CT Scan',
        'MRI',
        'Endoskopi',
        'Rehab Medik',
        'IGD 24 Jam',
        'Persalinan',
        'Apotek',
        'Labor',
        'Rehab Medik Dasar',
        'USG',
        'EKG',
        'Lab Prolanis',
        'TB TCM',
        'Alat PRB',
        'Penunjang',
        'Optik',
        'Labor Prolanis',
        'Apotek PRB',
    ];

    public function run(): void
    {
        foreach (self::LAYANAN as $namaLayanan) {
            Layanan::updateOrCreate(
                ['nama_layanan' => $namaLayanan],
                ['nama_layanan' => $namaLayanan],
            );
        }
    }
}
