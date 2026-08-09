<?php

namespace App\Filament\Resources\FkrtlResource\Pages;

use App\Filament\Resources\FkrtlResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFkrtl extends CreateRecord
{
    protected static string $resource = FkrtlResource::class;

    protected array $bedCounts = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tipe'] = FkrtlResource::getFacilityType();
        $this->bedCounts = $data['tempat_tidur'] ?? [];

        unset($data['tempat_tidur']);

        return $data;
    }

    protected function afterCreate(): void
    {
        FkrtlResource::syncBedCounts($this->record, $this->bedCounts);
    }
}
