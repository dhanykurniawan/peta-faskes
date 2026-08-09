<?php

namespace App\Filament\Resources\FkrtlResource\Pages;

use App\Filament\Resources\FkrtlResource;
use App\Imports\FkrtlExcelImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\HtmlString;

class ListFkrtl extends ListRecords
{
    protected static string $resource = FkrtlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Import ulang data FKRTL?')
                ->modalDescription('Import ini akan menghapus semua data FKRTL yang ada, lalu menggantinya dengan data dari file Excel.')
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
                                '<a href="' . asset('templates/template_fkrtl.xlsx') . '" download 
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
                        new FkrtlExcelImport(),
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
