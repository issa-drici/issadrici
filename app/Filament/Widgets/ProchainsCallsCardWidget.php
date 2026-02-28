<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProspectResource;
use App\Models\Call;
use Filament\Widgets\Widget;

class ProchainsCallsCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.prochains-calls-card';
    
    protected static ?int $sort = 2;
    
    public int | string | array $columnSpan = 1;
    
    public static function canView(): bool
    {
        return true;
    }
    
    public function getCalls()
    {
        return Call::query()
            ->where('statut', 'planifie')
            ->where('date_planifiee', '>=', now())
            ->with('prospect')
            ->orderBy('date_planifiee', 'asc')
            ->limit(5)
            ->get();
    }
}
