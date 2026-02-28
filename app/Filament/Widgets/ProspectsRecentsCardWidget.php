<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProspectResource;
use App\Models\Prospect;
use Filament\Widgets\Widget;

class ProspectsRecentsCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.prospects-recents-card';
    
    protected static ?int $sort = 5;
    
    public int | string | array $columnSpan = 1;
    
    public static function canView(): bool
    {
        return true;
    }
    
    public function getProspectsRecents()
    {
        return Prospect::query()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }
}
