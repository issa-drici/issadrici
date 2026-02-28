<?php

namespace App\Filament\Resources\PreparationMessageResource\Pages;

use App\Filament\Resources\PreparationMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPreparationMessage extends ViewRecord
{
    protected static string $resource = PreparationMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
