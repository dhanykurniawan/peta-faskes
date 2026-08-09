<?php

namespace App\Imports;

use App\Filament\Resources\FkrtlResource;
use App\Models\Fasilitas;
use App\Models\KabupatenKota;
use App\Models\Kecamatan;
use App\Models\Layanan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FkrtlExcelImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'FKRTL' => new FkrtlSheetImport(),
        ];
    }
}

class FkrtlSheetImport implements ToCollection, WithStartRow
{
    private const LAYANAN_COLUMNS = [
        17 => 'HD',
        18 => 'Kemoterapi',
        19 => 'Radioterapi',
        20 => 'Cathlab',
        21 => 'Labor PK',
        22 => 'Labor PA',
        23 => 'Rongent',
        24 => 'Ro Panoramic',
        25 => 'Echocardiografi',
        26 => 'ESWL',
        27 => 'Fakoemulsifikasi Set',
        28 => 'CT Scan',
        29 => 'MRI',
        30 => 'Endoskopi',
        31 => 'Rehab Medik',
    ];

    private const BED_COLUMNS = [
        9  => 'kelas_1',
        10 => 'kelas_2',
        11 => 'kelas_3',
        12 => 'icu',
        13 => 'iccu',
        14 => 'nicu',
        15 => 'hcu',
        16 => 'perinatologi',
    ];

    public function startRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows): void
    {
        DB::transaction(function () use ($rows) {
            Fasilitas::where('tipe', FkrtlResource::getFacilityType())->delete();

            foreach ($rows as $row) {
                $namaFaskes = $this->stringValue($row[6] ?? null);

                if ($namaFaskes === null) {
                    continue;
                }

                $kabkota = $this->resolveKabkota(
                    $this->stringValue($row[1] ?? null),
                    $this->integerValue($row[2] ?? null),
                );

                $kecamatan = $this->resolveKecamatan(
                    $this->stringValue($row[3] ?? null),
                    $kabkota,
                    $this->integerValue($row[4] ?? null),
                );

                if (!$kabkota || !$kecamatan) {
                    continue;
                }

                $fasilitas = Fasilitas::create([
                    'nama'          => $namaFaskes,
                    'tipe'          => FkrtlResource::getFacilityType(),
                    'tipe_detail'   => $this->stringValue($row[7] ?? null),
                    'kelas'         => $this->stringValue($row[8] ?? null),
                    'kode_faskes'   => $this->stringValue($row[5] ?? null),
                    'kantor_cabang' => $this->stringValue($row[0] ?? null),
                    'kabkota_id'    => $kabkota->id,
                    'kecamatan_id'  => $kecamatan->id,
                    'status_aktif'  => true,
                ]);

                FkrtlResource::syncBedCounts($fasilitas, $this->bedCounts($row));
                $fasilitas->layanans()->sync($this->layananIds($row));
            }
        });
    }

    private function resolveKabkota(?string $nama, ?int $populasi): ?KabupatenKota
    {
        if ($nama === null) {
            return null;
        }

        $kabkota = KabupatenKota::firstOrCreate(
            ['nama' => $nama],
            ['kode_bps' => null, 'populasi' => 0],
        );

        if ($populasi !== null) {
            $kabkota->update(['populasi' => $populasi]);
        }

        return $kabkota;
    }

    private function resolveKecamatan(?string $nama, ?KabupatenKota $kabkota, ?int $populasi): ?Kecamatan
    {
        if ($nama === null || !$kabkota) {
            return null;
        }

        $kecamatan = Kecamatan::firstOrCreate(
            ['nama' => $nama, 'kabkota_id' => $kabkota->id],
            ['populasi' => 0],
        );

        if ($populasi !== null) {
            $kecamatan->update(['populasi' => $populasi]);
        }

        return $kecamatan;
    }

    private function bedCounts(Collection $row): array
    {
        $counts = [];

        foreach (self::BED_COLUMNS as $column => $key) {
            $counts[$key] = $this->integerValue($row[$column] ?? null) ?? 0;
        }

        return $counts;
    }

    private function layananIds(Collection $row): array
    {
        $ids = [];

        foreach (self::LAYANAN_COLUMNS as $column => $namaLayanan) {
            if (!$this->hasValue($row[$column] ?? null)) {
                continue;
            }

            $ids[] = Layanan::firstOrCreate(['nama_layanan' => $namaLayanan])->id;
        }

        return $ids;
    }

    private function stringValue(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : $value;

        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function integerValue(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(['.', ','], ['', ''], (string) $value);

        return is_numeric($value) ? (int) $value : null;
    }

    private function hasValue(mixed $value): bool
    {
        return $this->stringValue($value) !== null;
    }
}