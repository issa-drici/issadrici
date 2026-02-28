<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProspectResource;
use App\Models\Prospect;
use Filament\Widgets\Widget;

class PropositionsEnAttenteCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.propositions-en-attente-card';
    
    protected static ?int $sort = 4;
    
    public int | string | array $columnSpan = 1;
    
    public static function canView(): bool
    {
        return true;
    }
    
    public function getPropositions()
    {
        return Prospect::query()
            ->where('statut', 'proposition_envoyee')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
    }
}
