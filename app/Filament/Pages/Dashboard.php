<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActionsEnRetardCardWidget;
use App\Filament\Widgets\ContenusPlanifiesCardWidget;
use App\Filament\Widgets\ContenusRecentsCardWidget;
use App\Filament\Widgets\DashboardStatsWidget;
use App\Filament\Widgets\InteractionsRecentesCardWidget;
use App\Filament\Widgets\ProchainsCallsCardWidget;
use App\Filament\Widgets\ProchainesActionsCardWidget;
use App\Filament\Widgets\PropositionsEnAttenteCardWidget;
use App\Filament\Widgets\ProspectsRecentsCardWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Tableau de bord';
    
    protected static ?int $navigationSort = 1;
    
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
        ];
    }
    
    public function getWidgets(): array
    {
        return [
            ActionsEnRetardCardWidget::class,
            ProchainsCallsCardWidget::class,
            ProchainesActionsCardWidget::class,
            PropositionsEnAttenteCardWidget::class,
            ProspectsRecentsCardWidget::class,
            InteractionsRecentesCardWidget::class,
            ContenusPlanifiesCardWidget::class,
            ContenusRecentsCardWidget::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return 2;
    }
}
