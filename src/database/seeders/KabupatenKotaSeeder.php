<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KabupatenKota;

class KabupatenKotaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Kota Bukittinggi',      'kode_bps' => '1375', 'populasi' => 0],
            ['nama' => 'Kota Padang Panjang',   'kode_bps' => '1373', 'populasi' => 0],
            ['nama' => 'Kabupaten Agam',         'kode_bps' => '1306', 'populasi' => 0],
            ['nama' => 'Kabupaten Pasaman',      'kode_bps' => '1308', 'populasi' => 0],
            ['nama' => 'Kabupaten Pasaman Barat','kode_bps' => '1312', 'populasi' => 0],
        ];

        foreach ($data as $item) {
            KabupatenKota::updateOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
