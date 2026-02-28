<?php

namespace App\Filament\Resources\ContenuResource\Pages;

use App\Filament\Resources\ContenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContenu extends ViewRecord
{
    protected static string $resource = ContenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
