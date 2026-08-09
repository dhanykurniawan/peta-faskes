<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KabupatenKotaResource\Pages;
use App\Filament\Resources\KabupatenKotaResource\RelationManagers;
use App\Models\KabupatenKota;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KabupatenKotaResource extends Resource
{
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Kabupaten/ Kota'; // -> nama di sidebar
    protected static ?string $pluralModelLabel = 'Kabupaten/ Kota'; // -> nama singular (muncul di tombol "Buat ...")
    protected static ?string $modelLabel = 'Kabupaten/ Kota'; // -> nama plural (muncul di judul halaman list)

    protected static ?string $model = KabupatenKota::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama')->required(), 
            Forms\Components\TextInput::make('kode_bps')->label('Kode BPS'), 
            Forms\Components\TextInput::make('populasi')->numeric()->default(0)]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')->label('No.')->rowIndex()->alignCenter(),
                Tables\Columns\TextColumn::make('nama')->searchable()->sortable(), 
                Tables\Columns\TextColumn::make('kode_bps')->label('Kode BPS')->alignCenter(),
                Tables\Columns\TextColumn::make('populasi')->numeric()->sortable()->alignCenter(), 
                Tables\Columns\TextColumn::make('kecamatans_count')->label('Jumlah Kecamatan')->counts('kecamatans')->alignCenter(),])
            ->filters([])
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
            'index' => Pages\ListKabupatenKotas::route('/'),
            'create' => Pages\CreateKabupatenKota::route('/create'),
            'edit' => Pages\EditKabupatenKota::route('/{record}/edit'),
        ];
    }
}
