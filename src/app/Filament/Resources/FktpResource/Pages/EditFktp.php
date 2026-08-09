<?php

namespace App\Filament\Resources\FktpResource\Pages;

use App\Filament\Resources\FktpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFktp extends EditRecord
{
    protected static string $resource = FktpResource::class;

    protected array $serviceStatuses = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['status_layanan'] = FktpResource::getServiceStatuses($this->record);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['tipe'] = FktpResource::getFacilityType();
        $this->serviceStatuses = $data['status_layanan'] ?? [];

        unset($data['status_layanan']);

        return $data;
    }

    protected function afterSave(): void
    {
        FktpResource::syncServiceStatuses($this->record, $this->serviceStatuses);
    }
}
