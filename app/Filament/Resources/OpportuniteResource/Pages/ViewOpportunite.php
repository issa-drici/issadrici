<?php

namespace App\Filament\Resources\OpportuniteResource\Pages;

use App\Filament\Resources\OpportuniteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOpportunite extends ViewRecord
{
    protected static string $resource = OpportuniteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->label('Actions')
            ->icon('heroicon-o-ellipsis-vertical')
            ->color('gray')
            ->button(),
        ];
    }
}
