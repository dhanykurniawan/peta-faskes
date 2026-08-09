<?php

namespace App\Imports;

use App\Models\Fasilitas;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\KabupatenKota;
use App\Models\Kecamatan;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class FasilitasImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function __construct(private ?string $facilityType = null)
    {
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // cari kabkota by nama
        $kabkota = KabupatenKota::whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($row['kabupaten_kota']) . '%'])->first();
        $kecamatan = Kecamatan::whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($row['kecamatan']) . '%'])
            ->when($kabkota, fn($q) => $q->where('kabkota_id', $kabkota->id))
            ->first();

        if (!$kabkota || !$kecamatan) {
            return null;
        } // skip row kalau wilayah ga ketemu

        return new Fasilitas([
            'nama' => $row['nama_faskes'],
            'tipe' => $this->facilityType ?? strtoupper($row['tipe']), // FKTP / FKRTL
            'tipe_detail' => $row['tipe_detail'] ?? null,
            'kelas' => $row['kelas'] ?? null,
            'kabkota_id' => $kabkota->id,
            'kecamatan_id' => $kecamatan->id,
            'alamat' => $row['alamat'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'email' => $row['email'] ?? null,
            'website' => $row['website'] ?? null,
            'lat' => $row['lat'] ?? null,
            'lng' => $row['lng'] ?? null,
            'status_aktif' => true,
        ]);
    }
}
