<?php

namespace App\Filament\Resources\OpportuniteResource\Pages;

use App\Filament\Resources\OpportuniteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOpportunite extends EditRecord
{
    protected static string $resource = OpportuniteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
