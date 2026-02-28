<?php

namespace App\Filament\Widgets;

use App\Models\Interaction;
use Filament\Widgets\Widget;

class InteractionsRecentesCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.interactions-recentes-card';
    
    protected static ?int $sort = 6;
    
    public int | string | array $columnSpan = 1;
    
    public static function canView(): bool
    {
        return true;
    }
    
    public function getInteractionsRecentes()
    {
        return Interaction::query()
            ->with('prospect')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
    }
}
