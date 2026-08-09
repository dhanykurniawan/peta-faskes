<?php

namespace App\Filament\Resources\FkrtlResource\Pages;

use App\Filament\Resources\FkrtlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFkrtl extends EditRecord
{
    protected static string $resource = FkrtlResource::class;

    protected array $bedCounts = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['tempat_tidur'] = FkrtlResource::getBedCounts($this->record);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['tipe'] = FkrtlResource::getFacilityType();
        $this->bedCounts = $data['tempat_tidur'] ?? [];

        unset($data['tempat_tidur']);

        return $data;
    }

    protected function afterSave(): void
    {
        FkrtlResource::syncBedCounts($this->record, $this->bedCounts);
    }
}
