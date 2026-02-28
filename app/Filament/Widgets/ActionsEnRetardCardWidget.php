<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProspectResource;
use App\Models\Prospect;
use Filament\Widgets\Widget;

class ActionsEnRetardCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.actions-en-retard-card';
    
    protected static ?int $sort = 1;
    
    public int | string | array $columnSpan = 1;
    
    public static function canView(): bool
    {
        return true;
    }
    
    public function getActionsEnRetard()
    {
        return Prospect::query()
            ->whereNotNull('prochaine_action')
            ->whereNotNull('date_prochaine_action')
            ->where('date_prochaine_action', '<', now())
            ->whereIn('statut', ['a_contacter', 'contacte', 'en_discussion', 'call_planifie', 'call_realise', 'proposition_envoyee'])
            ->orderBy('date_prochaine_action', 'asc')
            ->limit(5)
            ->get();
    }
}
