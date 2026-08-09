<?php

namespace App\Imports;

use App\Filament\Resources\FktpResource;
use App\Models\Fasilitas;
use App\Models\KabupatenKota;
use App\Models\Kecamatan;
use App\Models\Layanan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FktpExcelImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'FKTP' => new FktpSheetImport(),
        ];
    }
}

class FktpSheetImport implements ToCollection, WithStartRow
{
    private const LAYANAN_COLUMNS = [
        13 => 'IGD 24 Jam',
        17 => 'Rehab Medik Dasar',
        18 => 'USG',
        19 => 'EKG',
        20 => 'Lab Prolanis',
        21 => 'TB TCM',
        22 => 'Alat PRB',
        23 => 'Penunjang',
        24 => 'Optik',
        25 => 'Labor Prolanis',
        26 => 'Apotek PRB',
    ];

    private const STATUS_LAYANAN_COLUMNS = [
        14 => 'Persalinan',
        15 => 'Apotek',
        16 => 'Labor',
    ];

    public function startRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows): void
    {
        DB::transaction(function () use ($rows) {
            Fasilitas::where('tipe', FktpResource::getFacilityType())->delete();

            foreach ($rows as $row) {
                $namaFaskes = $this->stringValue($row[5] ?? null);

                if ($namaFaskes === null) {
                    continue;
                }

                $kabkota = $this->resolveKabkota($this->stringValue($row[1] ?? null));
                $kecamatan = $this->resolveKecamatan(
                    $this->stringValue($row[2] ?? null),
                    $kabkota,
                    $this->integerValue($row[3] ?? null),
                );

                if (!$kabkota || !$kecamatan) {
                    continue;
                }

                $fasilitas = Fasilitas::create([
                    'nama' => $namaFaskes,
                    'tipe' => FktpResource::getFacilityType(),
                    'tipe_detail' => $this->stringValue($row[7] ?? null),
                    'tipe_fktp' => $this->stringValue($row[8] ?? null),
                    'kelas' => $this->stringValue($row[7] ?? null),
                    'kode_faskes' => $this->stringValue($row[4] ?? null),
                    'kantor_cabang' => $this->stringValue($row[0] ?? null),
                    'kebutuhan_du' => $this->integerValue($row[6] ?? null) ?? 0,
                    'peserta_terdaftar' => $this->integerValue($row[9] ?? null) ?? 0,
                    'prolanis_dm' => $this->integerValue($row[10] ?? null) ?? 0,
                    'prolanis_ht' => $this->integerValue($row[11] ?? null) ?? 0,
                    'peserta_prb' => $this->integerValue($row[12] ?? null) ?? 0,
                    'kabkota_id' => $kabkota->id,
                    'kecamatan_id' => $kecamatan->id,
                    'status_aktif' => true,
                ]);

                $fasilitas->layanans()->sync($this->layananIds($row));
                FktpResource::syncServiceStatuses($fasilitas, $this->serviceStatuses($row));
            }
        });
    }

    private function resolveKabkota(?string $nama): ?KabupatenKota
    {
        if ($nama === null) {
            return null;
        }

        $nama = $this->normalizeKabkotaName($nama);

        return KabupatenKota::firstOrCreate(
            ['nama' => $nama],
            ['kode_bps' => null, 'populasi' => 0],
        );
    }

    private function resolveKecamatan(?string $nama, ?KabupatenKota $kabkota, ?int $populasi): ?Kecamatan
    {
        if ($nama === null || !$kabkota) {
            return null;
        }

        $nama = $this->normalizeName($nama);
        $kecamatan = Kecamatan::query()
            ->where('kabkota_id', $kabkota->id)
            ->whereRaw('LOWER(nama) = ?', [strtolower($nama)])
            ->first();

        if (!$kecamatan) {
            $kecamatan = Kecamatan::create([
                'nama' => $nama,
                'kabkota_id' => $kabkota->id,
                'populasi' => 0,
            ]);
        }

        if ($populasi !== null) {
            $kecamatan->update(['populasi' => $populasi]);
        }

        return $kecamatan;
    }

    private function layananIds(Collection $row): array
    {
        $ids = [];

        foreach (self::LAYANAN_COLUMNS as $column => $namaLayanan) {
            if (!$this->hasServiceValue($row[$column] ?? null)) {
                continue;
            }

            $ids[] = Layanan::firstOrCreate(['nama_layanan' => $namaLayanan])->id;
        }

        return $ids;
    }

    private function serviceStatuses(Collection $row): array
    {
        $statuses = [];

        foreach (self::STATUS_LAYANAN_COLUMNS as $column => $namaLayanan) {
            $statuses[$namaLayanan] = $this->serviceStatusValue($row[$column] ?? null);
        }

        return $statuses;
    }

    private function normalizeKabkotaName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));
        $upper = strtoupper($name);

        if (str_starts_with($upper, 'KAB. ')) {
            return 'Kabupaten ' . $this->normalizeName(substr($name, 5));
        }

        if (str_starts_with($upper, 'KABUPATEN ')) {
            return 'Kabupaten ' . $this->normalizeName(substr($name, 10));
        }

        if (str_starts_with($upper, 'KOTA ')) {
            return 'Kota ' . $this->normalizeName(substr($name, 5));
        }

        return $this->normalizeName($name);
    }

    private function normalizeName(string $name): string
    {
        $name = Str::title(strtolower(trim(preg_replace('/\s+/', ' ', $name))));

        return preg_replace_callback(
            '/\b(I|Ii|Iii|Iv|Vi|Vii|Viii|Ix|Xi|Xii)\b/',
            fn(array $matches) => strtoupper($matches[0]),
            $name,
        );
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

    private function hasServiceValue(mixed $value): bool
    {
        $value = strtolower((string) ($this->stringValue($value) ?? ''));

        return !in_array($value, ['', 'tidak', 'tidak ada', '0', '-'], true);
    }

    private function serviceStatusValue(mixed $value): string
    {
        $value = strtolower((string) ($this->stringValue($value) ?? ''));

        return match ($value) {
            '1 atap', 'satu atap' => '1 Atap',
            'jejaring' => 'Jejaring',
            default => 'Tidak Ada',
        };
    }
}
