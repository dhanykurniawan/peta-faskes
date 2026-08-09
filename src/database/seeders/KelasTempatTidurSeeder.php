<?php

namespace Database\Seeders;

use App\Models\KelasTempatTidur;
use Illuminate\Database\Seeder;

class KelasTempatTidurSeeder extends Seeder
{
    public function run(): void
    {
        foreach (KelasTempatTidur::DEFAULT_CLASSES as $index => $kelas) {
            KelasTempatTidur::updateOrCreate(
                ['kode' => $kelas['kode']],
                [
                    'nama' => $kelas['nama'],
                    'urutan' => $index + 1,
                ],
            );
        }
    }
}
