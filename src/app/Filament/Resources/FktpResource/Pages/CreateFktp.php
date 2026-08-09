<?php

namespace App\Filament\Resources\FktpResource\Pages;

use App\Filament\Resources\FktpResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFktp extends CreateRecord
{
    protected static string $resource = FktpResource::class;

    protected array $serviceStatuses = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tipe'] = FktpResource::getFacilityType();
        $this->serviceStatuses = $data['status_layanan'] ?? [];

        unset($data['status_layanan']);

        return $data;
    }

    protected function afterCreate(): void
    {
        FktpResource::syncServiceStatuses($this->record, $this->serviceStatuses);
    }
}
