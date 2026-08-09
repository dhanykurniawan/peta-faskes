<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\ManagesFaskesResource;
use App\Filament\Resources\FkrtlResource\Pages;
use App\Models\Fasilitas;
use App\Models\KelasTempatTidur;
use Filament\Forms;
use Filament\Resources\Resource;

class FkrtlResource extends Resource
{
    use ManagesFaskesResource;

    protected static ?string $navigationGroup = 'Fasilitas Kesehatan';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'FKRTL'; // -> nama di sidebar
    protected static ?string $pluralModelLabel = 'FKRTL'; // -> nama singular (muncul di tombol "Buat ...")
    protected static ?string $modelLabel = 'FKRTL'; // -> nama plural (muncul di judul halaman list)
    protected static ?string $slug = 'fkrtl';

    protected static ?string $model = Fasilitas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getFacilityType(): string
    {
        return 'FKRTL';
    }

    public static function getBedClassOptions(): array
    {
        return KelasTempatTidur::query()
            ->orderBy('urutan')
            ->get(['kode', 'nama'])
            ->map(fn(KelasTempatTidur $kelas) => [
                'kode' => $kelas->kode,
                'nama' => $kelas->nama,
            ])
            ->whenEmpty(fn() => collect(KelasTempatTidur::DEFAULT_CLASSES))
            ->all();
    }

    public static function getBedCounts(Fasilitas $record): array
    {
        $counts = $record->fasilitasTempatTidurs()
            ->with('kelasTempatTidur:id,kode')
            ->get()
            ->pluck('jumlah', 'kelasTempatTidur.kode')
            ->all();

        return collect(static::getBedClassOptions())
            ->mapWithKeys(fn(array $kelas) => [$kelas['kode'] => (int) ($counts[$kelas['kode']] ?? 0)])
            ->all();
    }

    public static function syncBedCounts(Fasilitas $record, array $bedCounts): void
    {
        foreach (static::getBedClassOptions() as $index => $kelas) {
            $bedClass = KelasTempatTidur::firstOrCreate(
                ['kode' => $kelas['kode']],
                [
                    'nama' => $kelas['nama'],
                    'urutan' => $index + 1,
                ],
            );

            $record->fasilitasTempatTidurs()->updateOrCreate(
                ['kelas_tempat_tidur_id' => $bedClass->id],
                ['jumlah' => max(0, (int) ($bedCounts[$kelas['kode']] ?? 0))],
            );
        }
    }

    protected static function getAdditionalFormSections(): array
    {
        return [
            Forms\Components\Section::make('Jumlah Tempat Tidur Rawat Inap')
                ->schema(
                    collect(static::getBedClassOptions())
                        ->map(fn(array $kelas) => Forms\Components\TextInput::make('tempat_tidur.' . $kelas['kode'])
                            ->label($kelas['nama'])
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0))
                        ->all(),
                )
                ->columns(2),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFkrtl::route('/'),
            'create' => Pages\CreateFkrtl::route('/create'),
            'edit' => Pages\EditFkrtl::route('/{record}/edit'),
        ];
    }
}
