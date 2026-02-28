<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContenuResource;
use App\Models\Contenu;
use Filament\Widgets\Widget;

class ContenusRecentsCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.contenus-recents-card';
    
    protected static ?int $sort = 8;
    
    public int | string | array $columnSpan = 1;
    
    public static function canView(): bool
    {
        return true;
    }
    
    public function getContenus()
    {
        return Contenu::query()
            ->where('statut', 'publie')
            ->whereNotNull('date_publication_reelle')
            ->orderBy('date_publication_reelle', 'desc')
            ->limit(5)
            ->get();
    }
}
