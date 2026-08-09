<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\ManagesFaskesResource;
use App\Filament\Resources\FktpResource\Pages;
use App\Models\Fasilitas;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Resources\Resource;

class FktpResource extends Resource
{
    use ManagesFaskesResource;

    protected static ?string $navigationGroup = 'Fasilitas Kesehatan';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'FKTP';
    protected static ?string $pluralModelLabel = 'FKTP';
    protected static ?string $modelLabel = 'FKTP';
    protected static ?string $slug = 'fktp';

    protected static ?string $model = Fasilitas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public const STATUS_LAYANAN_OPTIONS = [
        '1 Atap' => '1 Atap',
        'Jejaring' => 'Jejaring',
        'Tidak Ada' => 'Tidak Ada',
    ];

    public const STATUS_LAYANAN_NAMES = [
        'Persalinan',
        'Apotek',
        'Labor',
    ];

    public static function getFacilityType(): string
    {
        return 'FKTP';
    }

    protected static function getAdditionalFormSections(): array
    {
        return [
            Forms\Components\Section::make('Jumlah Peserta Terdaftar')
                ->schema([
                    Forms\Components\TextInput::make('peserta_terdaftar')->numeric()->integer()->minValue(0)->default(0),
                    Forms\Components\TextInput::make('prolanis_dm')->label('Prolanis DM')->numeric()->integer()->minValue(0)->default(0),
                    Forms\Components\TextInput::make('prolanis_ht')->label('Prolanis HT')->numeric()->integer()->minValue(0)->default(0),
                    Forms\Components\TextInput::make('peserta_prb')->label('Peserta PRB')->numeric()->integer()->minValue(0)->default(0),
                ])
                ->columns(2),
        ];
    }

    protected static function getLayananFormComponents(): array
    {
        return [
            Forms\Components\CheckboxList::make('layanans')
                ->label('Layanan')
                ->relationship(
                    'layanans',
                    'nama_layanan',
                    modifyQueryUsing: fn($query) => $query->whereNotIn('nama_layanan', self::STATUS_LAYANAN_NAMES),
                )
                ->columns(3),

            Forms\Components\Grid::make(3)
                ->schema(
                    collect(self::STATUS_LAYANAN_NAMES)
                        ->map(fn(string $namaLayanan) => Forms\Components\Select::make('status_layanan.' . $namaLayanan)
                            ->label($namaLayanan)
                            ->options(self::STATUS_LAYANAN_OPTIONS)
                            ->default('Tidak Ada')
                            ->required())
                        ->all(),
                ),
        ];
    }

    public static function getServiceStatuses(Fasilitas $record): array
    {
        $statuses = $record->layanans()
            ->whereIn('nama_layanan', self::STATUS_LAYANAN_NAMES)
            ->get()
            ->pluck('pivot.status_layanan', 'nama_layanan')
            ->all();

        return collect(self::STATUS_LAYANAN_NAMES)
            ->mapWithKeys(fn(string $namaLayanan) => [$namaLayanan => $statuses[$namaLayanan] ?? 'Tidak Ada'])
            ->all();
    }

    public static function syncServiceStatuses(Fasilitas $record, array $statuses): void
    {
        foreach (self::STATUS_LAYANAN_NAMES as $namaLayanan) {
            $status = $statuses[$namaLayanan] ?? 'Tidak Ada';
            $layanan = Layanan::firstOrCreate(['nama_layanan' => $namaLayanan]);

            if ($status === 'Tidak Ada') {
                $record->layanans()->detach($layanan->id);
                continue;
            }

            $record->layanans()->syncWithoutDetaching([
                $layanan->id => ['status_layanan' => $status],
            ]);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFktp::route('/'),
            'create' => Pages\CreateFktp::route('/create'),
            'edit' => Pages\EditFktp::route('/{record}/edit'),
        ];
    }
}
