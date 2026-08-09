<?php

namespace App\Filament\Resources\FktpResource\Pages;

use App\Filament\Resources\FktpResource;
use App\Imports\FktpExcelImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\HtmlString;

class ListFktp extends ListRecords
{
    protected static string $resource = FktpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Import ulang data FKTP?')
                ->modalDescription('Import ini akan menghapus semua data FKTP yang ada, lalu menggantinya dengan data dari file Excel.')
                // ->form([
                //     FileUpload::make('file')
                //         ->label('File Excel')
                //         ->acceptedFileTypes([
                //             'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                //             'application/vnd.ms-excel',
                //         ])
                //         ->required(),
                // ])
                ->form([
                        \Filament\Forms\Components\Placeholder::make('download_template')
                            ->label('')
                            ->content(new HtmlString(
                                '<a href="' . asset('templates/template_fktp.xlsx') . '" download 
                                    class="text-primary-600 underline text-sm">
                                    ⬇ Download template Excel
                                </a>'
                            )),

                        FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                            ])
                            ->required(),
                    ])
                ->action(function (array $data) {
                    Excel::import(
                        new FktpExcelImport(),
                        storage_path('app/public/' . $data['file'])
                    );

                    \Filament\Notifications\Notification::make()
                        ->title('Import berhasil!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
