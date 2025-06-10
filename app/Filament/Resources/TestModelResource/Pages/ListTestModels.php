<?php

namespace App\Filament\Resources\TestModelResource\Pages;

use App\Filament\Resources\TestModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;


class ListTestModels extends ListRecords
{
    protected static string $resource = TestModelResource::class;

    protected function getHeaderActions(): array
    {
        // return [
        //     Actions\CreateAction::make(),
        // ];
        return [
            Actions\Action::make('openModal')
                ->label('Test Modal')
                ->modalHeading('Test Modal')
                ->modalContent(fn () => view('filament-tables::components.test-modal-content'))
                ->modalSubmitActionLabel('Close'),
        ];
    }
}
