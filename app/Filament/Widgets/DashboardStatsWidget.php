<?php

namespace App\Filament\Widgets;

use App\Models\Prospect;
use App\Models\Call;
use App\Models\Opportunite;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Prospects actifs', Prospect::whereIn('statut', ['a_contacter', 'contacte', 'en_discussion', 'call_planifie', 'call_realise', 'proposition_envoyee'])->count())
                ->description('En cours de prospection')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Conversations en cours', Prospect::where('statut', 'en_discussion')->count())
                ->description('En discussion active')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),
            Stat::make('Calls planifiés', Call::where('statut', 'planifie')->where('date_planifiee', '>=', now())->count())
                ->description('À venir')
                ->descriptionIcon('heroicon-m-phone')
                ->color('warning'),
            Stat::make('Propositions envoyées', Prospect::where('statut', 'proposition_envoyee')->count())
                ->description('En attente de réponse')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
            Stat::make('Opportunités ouvertes', Opportunite::whereNotIn('stade', ['gagne', 'perdu'])->count())
                ->description('En cours')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),
            Stat::make('Projets gagnés', Opportunite::where('stade', 'gagne')->count())
                ->description('Total')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
        ];
    }
}
