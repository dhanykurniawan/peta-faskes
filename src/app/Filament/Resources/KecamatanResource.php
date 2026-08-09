<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KecamatanResource\Pages;
use App\Filament\Resources\KecamatanResource\RelationManagers;
use App\Models\Kecamatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KecamatanResource extends Resource
{
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Kecamatan'; // -> nama di sidebar
    protected static ?string $pluralModelLabel = 'Kecamatan'; // -> nama singular (muncul di tombol "Buat ...")
    protected static ?string $modelLabel = 'Kecamatan'; // -> nama plural (muncul di judul halaman list)

    protected static ?string $model = Kecamatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('kabkota_id')
                ->label('Kabupaten/Kota')
                ->relationship('kabupatenKota', 'nama')
                ->searchable()
                ->preload() // ← tambah ini
                ->required(),
            Forms\Components\TextInput::make('nama')->required(),
            Forms\Components\TextInput::make('kode_bps')->label('Kode BPS'),
            Forms\Components\TextInput::make('populasi')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')->label('No.')->rowIndex()->alignCenter(),
                Tables\Columns\TextColumn::make('kabupatenKota.nama')->label('Kabupaten/Kota')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kode_bps')->label('Kode BPS')->alignCenter(),
                Tables\Columns\TextColumn::make('populasi')->numeric()->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('fktp_count')
                    ->label('Jumlah FKTP')
                    ->alignCenter()
                    ->counts(['fktp' => fn($query) => $query->where('tipe', 'FKTP')]),

                Tables\Columns\TextColumn::make('fkrtl_count')
                    ->label('Jumlah FKRTL')
                    ->alignCenter()
                    ->counts([
                        'fkrtl' => fn($query) => $query->where('tipe', 'FKRTL'),
                    ]),
            ])
            ->filters([Tables\Filters\SelectFilter::make('kabkota_id')->label('Kabupaten/Kota')->relationship('kabupatenKota', 'nama')])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKecamatans::route('/'),
            'create' => Pages\CreateKecamatan::route('/create'),
            'edit' => Pages\EditKecamatan::route('/{record}/edit'),
        ];
    }
}
