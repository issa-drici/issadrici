<?php

namespace App\Filament\Resources\CallResource\Pages;

use App\Filament\Resources\CallResource;
use App\Filament\Resources\ProspectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCall extends ViewRecord
{
    protected static string $resource = CallResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Charger la relation booking avec les données du formulaire
        $this->record->load('booking');
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('voir_prospect')
                ->label('Voir le prospect')
                ->icon('heroicon-o-user')
                ->color('primary')
                ->url(fn () => $this->record->prospect 
                    ? ProspectResource::getUrl('view', ['record' => $this->record->prospect])
                    : null)
                ->visible(fn () => $this->record->prospect !== null)
                ->openUrlInNewTab(),
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
