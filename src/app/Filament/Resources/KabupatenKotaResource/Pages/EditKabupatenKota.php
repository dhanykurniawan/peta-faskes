<?php

namespace App\Filament\Resources\KabupatenKotaResource\Pages;

use App\Filament\Resources\KabupatenKotaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKabupatenKota extends EditRecord
{
    protected static string $resource = KabupatenKotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
