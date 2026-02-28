<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContenuResource;
use App\Models\Contenu;
use Filament\Widgets\Widget;

class ContenusPlanifiesCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.contenus-planifies-card';
    
    protected static ?int $sort = 7;
    
    public int | string | array $columnSpan = 1;
    
    public static function canView(): bool
    {
        return true;
    }
    
    public function getContenus()
    {
        return Contenu::query()
            ->where('statut', 'planifie')
            ->where('date_publication_planifiee', '>=', now())
            ->orderBy('date_publication_planifiee', 'asc')
            ->limit(5)
            ->get();
    }
}
