<?php

namespace Database\Seeders;

use App\Filament\Resources\FkrtlResource;
use App\Models\Fasilitas;
use App\Models\KabupatenKota;
use App\Models\Kecamatan;
use App\Models\Layanan;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->fasilitas() as $item) {
            $kabkota = KabupatenKota::where('nama', $item['kabkota'])->first();
            $kecamatan = Kecamatan::query()
                ->where('nama', $item['kecamatan'])
                ->when($kabkota, fn($query) => $query->where('kabkota_id', $kabkota->id))
                ->first();

            if (!$kabkota || !$kecamatan) {
                $this->command->warn("Wilayah tidak ditemukan untuk {$item['nama']}");
                continue;
            }

            $fasilitas = Fasilitas::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'tipe' => $item['tipe'],
                    'tipe_detail' => $item['tipe_detail'],
                    'tipe_fktp' => $item['tipe_fktp'] ?? null,
                    'kelas' => $item['kelas'],
                    'kode_faskes' => $item['kode_faskes'],
                    'kantor_cabang' => $item['kantor_cabang'],
                    'kebutuhan_du' => $item['kebutuhan_du'] ?? 0,
                    'peserta_terdaftar' => $item['peserta_terdaftar'] ?? 0,
                    'prolanis_dm' => $item['prolanis_dm'] ?? 0,
                    'prolanis_ht' => $item['prolanis_ht'] ?? 0,
                    'peserta_prb' => $item['peserta_prb'] ?? 0,
                    'kecamatan_id' => $kecamatan->id,
                    'kabkota_id' => $kabkota->id,
                    'alamat' => $item['alamat'],
                    'lat' => $item['lat'],
                    'lng' => $item['lng'],
                    'telepon' => $item['telepon'],
                    'email' => $item['email'],
                    'website' => $item['website'],
                    'status_aktif' => $item['status_aktif'],
                ],
            );

            $layananIds = Layanan::query()
                ->whereIn('nama_layanan', $item['layanans'])
                ->pluck('id')
                ->all();

            $fasilitas->layanans()->sync($layananIds);

            if ($fasilitas->tipe === 'FKRTL') {
                FkrtlResource::syncBedCounts($fasilitas, $item['tempat_tidur'] ?? []);
            }
        }
    }

    private function fasilitas(): array
    {
        return [
            [
                'nama' => 'RSUD Kota Padang Panjang',
                'tipe' => 'FKRTL',
                'tipe_detail' => 'RS Umum',
                'kelas' => 'C',
                'kode_faskes' => null,
                'kantor_cabang' => null,
                'kabkota' => 'Kota Padang Panjang',
                'kecamatan' => 'Padang Panjang Timur',
                'alamat' => null,
                'lat' => null,
                'lng' => null,
                'telepon' => null,
                'email' => null,
                'website' => null,
                'status_aktif' => true,
                'layanans' => ['Laboratorium', 'Radiologi'],
                'tempat_tidur' => [],
            ],
            [
                'nama' => 'RSI Ibnu Sina Padang Panjang',
                'tipe' => 'FKRTL',
                'tipe_detail' => 'RS Umum',
                'kelas' => 'C',
                'kode_faskes' => null,
                'kantor_cabang' => null,
                'kabkota' => 'Kota Padang Panjang',
                'kecamatan' => 'Padang Panjang Barat',
                'alamat' => 'Bukit Surungan',
                'lat' => -0.4608987,
                'lng' => 100.4005018,
                'telepon' => '0812212121',
                'email' => 'rsiyarsi_pp@gmail.com',
                'website' => 'http://www.yarsi.com',
                'status_aktif' => true,
                'layanans' => ['Laboratorium', 'Radiologi'],
                'tempat_tidur' => [],
            ],
            [
                'nama' => 'Puskesmas Kebun Sikolos',
                'tipe' => 'FKTP',
                'tipe_detail' => 'Puskesmas',
                'kelas' => null,
                'kode_faskes' => null,
                'kantor_cabang' => null,
                'kabkota' => 'Kota Padang Panjang',
                'kecamatan' => 'Padang Panjang Barat',
                'alamat' => null,
                'lat' => -0.46823,
                'lng' => 100.39868,
                'telepon' => null,
                'email' => null,
                'website' => null,
                'status_aktif' => true,
                'layanans' => [],
                'tempat_tidur' => [],
            ],
            [
                'nama' => 'Klinik Utama Putri Manggopoh',
                'tipe' => 'FKRTL',
                'tipe_detail' => 'Klinik Utama',
                'kelas' => 'KU',
                'kode_faskes' => null,
                'kantor_cabang' => null,
                'kabkota' => 'Kabupaten Agam',
                'kecamatan' => 'Lubuk Basung',
                'alamat' => null,
                'lat' => -0.2869408,
                'lng' => 99.9791563,
                'telepon' => null,
                'email' => null,
                'website' => null,
                'status_aktif' => true,
                'layanans' => [],
                'tempat_tidur' => [],
            ],
            [
                'nama' => 'RSIA Rizki Bunda',
                'tipe' => 'FKRTL',
                'tipe_detail' => 'RS Khusus',
                'kelas' => 'C',
                'kode_faskes' => null,
                'kantor_cabang' => null,
                'kabkota' => 'Kabupaten Agam',
                'kecamatan' => 'Lubuk Basung',
                'alamat' => null,
                'lat' => -0.3153,
                'lng' => 100.0297,
                'telepon' => null,
                'email' => null,
                'website' => null,
                'status_aktif' => true,
                'layanans' => [],
                'tempat_tidur' => [],
            ],
        ];
    }
}
