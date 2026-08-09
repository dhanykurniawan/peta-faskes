<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayananResource\Pages;
use App\Filament\Resources\LayananResource\RelationManagers;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LayananResource extends Resource
{
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Layanan'; // -> nama di sidebar
    protected static ?string $pluralModelLabel = 'Layanan'; // -> nama singular (muncul di tombol "Buat ...")
    protected static ?string $modelLabel = 'Layanan'; // -> nama plural (muncul di judul halaman list)

    protected static ?string $model = Layanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([Forms\Components\TextInput::make('nama_layanan')->required()->unique()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('no')->label('No.')->rowIndex()->alignCenter(),
            Tables\Columns\TextColumn::make('nama_layanan')->searchable()->sortable(), 
            Tables\Columns\TextColumn::make('fasilitas_count')->label('Jml Faskes')->counts('fasilitas')])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
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
            'index' => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit' => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }
}
