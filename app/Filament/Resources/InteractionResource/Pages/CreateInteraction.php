<?php

namespace App\Filament\Resources\InteractionResource\Pages;

use App\Filament\Resources\InteractionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInteraction extends CreateRecord
{
    protected static string $resource = InteractionResource::class;

    public function mount(): void
    {
        parent::mount();
        
        // Pré-remplir le prospect_id si passé en paramètre
        if (request()->has('prospect_id')) {
            $this->form->fill([
                'prospect_id' => request()->get('prospect_id'),
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si prospect_id est passé en paramètre d'URL, l'utiliser
        if (request()->has('prospect_id') && !isset($data['prospect_id'])) {
            $data['prospect_id'] = request()->get('prospect_id');
        }
        
        return $data;
    }
}
