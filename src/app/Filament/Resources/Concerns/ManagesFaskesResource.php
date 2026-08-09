<?php

namespace App\Filament\Resources\Concerns;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

trait ManagesFaskesResource
{
    abstract public static function getFacilityType(): string;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Profil Faskes')
                ->schema([
                    Forms\Components\TextInput::make('nama')->required(),
                    Forms\Components\Select::make('tipe')
                        ->options(['FKTP' => 'FKTP', 'FKRTL' => 'FKRTL'])
                        ->default(static::getFacilityType())
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->reactive(),
                    Forms\Components\Select::make('tipe_detail')->options(
                        fn(callable $get) => match ($get('tipe')) {
                            'FKTP' => [
                                'Puskesmas' => 'Puskesmas',
                                'Klinik Pratama' => 'Klinik Pratama',
                                'TPMD' => 'TPMD',
                                'TPMDG' => 'TPMDG',
                                'Dokter Praktek' => 'Dokter Praktek',
                            ],
                            'FKRTL' => [
                                'RS Umum' => 'RS Umum',
                                'RS Khusus' => 'RS Khusus',
                                'RS Khusus Ibu dan Anak' => 'RS Khusus Ibu dan Anak',
                                'RS Khusus Otak' => 'RS Khusus Otak',
                                'Klinik Utama' => 'Klinik Utama',
                                'KU Pratama Umum' => 'KU Pratama Umum',
                                'KU Rajal Umum' => 'KU Rajal Umum',
                                'KU Rajal Khusus Mata' => 'KU Rajal Khusus Mata',
                                'KU Rajal Khusus Jantung' => 'KU Rajal Khusus Jantung',
                            ],
                            default => [],
                        },
                    )->label(static::getFacilityType() === 'FKTP' ? 'Jenis FKTP' : 'Jenis FKRTL'),
                    Forms\Components\TextInput::make('tipe_fktp')
                        ->label('Tipe FKTP')
                        ->visible(static::getFacilityType() === 'FKTP'),
                    Forms\Components\TextInput::make('kode_faskes')->label(static::getFacilityType() === 'FKTP' ? 'Kode FKTP' : 'Kode FKRTL'),
                    Forms\Components\TextInput::make('kantor_cabang')->label('Kantor Cabang'),
                    Forms\Components\TextInput::make('kebutuhan_du')
                        ->label('Kebutuhan DU (ratio)')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->visible(static::getFacilityType() === 'FKTP'),
                    Forms\Components\TextInput::make('kelas')->label('Kelas'),
                    Forms\Components\Toggle::make('status_aktif')->default(true),
                    Forms\Components\Select::make('kabkota_id')->label('Kabupaten/Kota')->relationship('kabupatenKota', 'nama')->searchable()->required()->reactive()->preload(),
                    Forms\Components\Select::make('kecamatan_id')->label('Kecamatan')->options(fn(callable $get) => \App\Models\Kecamatan::where('kabkota_id', $get('kabkota_id'))->pluck('nama', 'id'))->searchable()->preload()->required(),
                    Forms\Components\TextInput::make('alamat'),
                    Forms\Components\TextInput::make('telepon'),
                    Forms\Components\TextInput::make('email')->email(),
                    Forms\Components\TextInput::make('website')->url(),
                    Forms\Components\TextInput::make('lat')->label('Latitude')->numeric(),
                    Forms\Components\TextInput::make('lng')->label('Longitude')->numeric(),
                ])
                ->columns(2),

            ...static::getAdditionalFormSections(),

            Forms\Components\Section::make('Layanan yang Tersedia')
                ->schema(static::getLayananFormComponents()),
        ]);
    }

    protected static function getAdditionalFormSections(): array
    {
        return [];
    }

    protected static function getLayananFormComponents(): array
    {
        return [
            Forms\Components\CheckboxList::make('layanans')->label('Layanan')->relationship('layanans', 'nama_layanan')->columns(3),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')->label('No.')->rowIndex()->alignCenter(),
                Tables\Columns\TextColumn::make('kode_faskes')->label('Kode')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('tipe')->colors(['primary' => 'FKTP', 'danger' => 'FKRTL'])->alignCenter(),
                Tables\Columns\TextColumn::make('tipe_detail')->alignCenter(),
                Tables\Columns\TextColumn::make('kelas')->alignCenter(),
                Tables\Columns\TextColumn::make('kabupatenKota.nama')->label('Kab/Kota')->sortable(),
                Tables\Columns\TextColumn::make('kecamatan.nama')->label('Kecamatan')->sortable(),
                Tables\Columns\IconColumn::make('status_aktif')->boolean()->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')->options(['FKTP' => 'FKTP', 'FKRTL' => 'FKRTL']),
                Tables\Filters\SelectFilter::make('kabkota_id')->label('Kabupaten/Kota')->relationship('kabupatenKota', 'nama'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tipe', static::getFacilityType());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
}
