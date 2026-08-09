<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KabupatenKota;
use App\Models\Kecamatan;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Kota Bukittinggi' => [
                'Aur Birugo Tigo Baleh',
                'Guguk Panjang',
                'Mandiangin Koto Selayan',
            ],
            'Kota Padang Panjang' => [
                'Padang Panjang Barat',
                'Padang Panjang Timur',
            ],
            'Kabupaten Agam' => [
                'Ampek Angkek',
                'Banuhampu',
                'Baso',
                'Canduang',
                'IV Angkat Candung',
                'IV Koto',
                'Kamang Magek',
                'Lubuk Basung',
                'Malalak',
                'Matur',
                'Palembayan',
                'Palupuh',
                'Sungai Pua',
                'Tanjung Mutiara',
                'Tanjung Raya',
                'Tilatang Kamang',
            ],
            'Kabupaten Pasaman' => [
                'Bonjol',
                'Dua Koto',
                'Lubuk Sikaping',
                'Mapat Tunggul',
                'Mapat Tunggul Selatan',
                'Padang Gelugur',
                'Panti',
                'Rao',
                'Rao Selatan',
                'Rao Utara',
                'Simpang Alahan Mati',
                'Tigo Nagari',
            ],
            'Kabupaten Pasaman Barat' => [
                'Gunung Tuleh',
                'Kinali',
                'Koto Balingka',
                'Lembah Malintang',
                'Luhak Nan Duo',
                'Pasaman',
                'Ranah Batahan',
                'Sasak Ranah Pasisie',
                'Sungai Aur',
                'Sungai Beremas',
                'Talamau',
            ],
        ];

        foreach ($data as $namaKabkota => $kecamatans) {
            $kabkota = KabupatenKota::where('nama', $namaKabkota)->first();

            if (!$kabkota) {
                $this->command->warn("Kabupaten/Kota tidak ditemukan: {$namaKabkota}");
                continue;
            }

            foreach ($kecamatans as $namaKecamatan) {
                Kecamatan::updateOrCreate(
                    ['nama' => $namaKecamatan, 'kabkota_id' => $kabkota->id],
                    ['nama' => $namaKecamatan, 'kabkota_id' => $kabkota->id, 'populasi' => 0]
                );
            }

            $this->command->info("✓ {$namaKabkota}: " . count($kecamatans) . " kecamatan");
        }
    }
}
