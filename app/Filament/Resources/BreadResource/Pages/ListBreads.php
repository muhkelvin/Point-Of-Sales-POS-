<?php

namespace App\Filament\Resources\BreadResource\Pages;

use App\Filament\Resources\BreadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBreads extends ListRecords
{
    protected static string $resource = BreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
