<?php

namespace App\Filament\Resources\BreadResource\Pages;

use App\Filament\Resources\BreadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBread extends EditRecord
{
    protected static string $resource = BreadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
