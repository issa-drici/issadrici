<?php

namespace App\Filament\Resources\InteractionResource\Pages;

use App\Filament\Resources\InteractionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInteraction extends ViewRecord
{
    protected static string $resource = InteractionResource::class;

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
