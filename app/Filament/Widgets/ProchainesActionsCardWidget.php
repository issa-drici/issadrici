<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContenuResource;
use App\Filament\Resources\ProspectResource;
use App\Models\Contenu;
use App\Models\Prospect;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ProchainesActionsCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.prochaines-actions-card';
    
    protected static ?int $sort = 3;
    
    public int | string | array $columnSpan = 1;
    
    public static function canView(): bool
    {
        return true;
    }
    
    public function getActions(): Collection
    {
        $prospects = Prospect::query()
            ->whereNotNull('prochaine_action')
            ->whereNotNull('date_prochaine_action')
            ->where('date_prochaine_action', '<=', now()->addDays(7))
            ->whereIn('statut', ['a_contacter', 'contacte', 'en_discussion', 'call_planifie', 'call_realise', 'proposition_envoyee'])
            ->get()
            ->map(function ($prospect) {
                return [
                    'type' => 'prospect',
                    'id' => $prospect->id,
                    'titre' => "{$prospect->prenom} {$prospect->nom}",
                    'sous_titre' => $prospect->societe,
                    'action' => $prospect->prochaine_action,
                    'date' => $prospect->date_prochaine_action,
                    'url' => ProspectResource::getUrl('view', ['record' => $prospect]),
                ];
            });
        
        $contenus = Contenu::query()
            ->where('statut', 'planifie')
            ->whereNotNull('date_publication_planifiee')
            ->where('date_publication_planifiee', '<=', now()->addDays(7))
            ->get()
            ->map(function ($contenu) {
                $plateformeLabel = match($contenu->plateforme) {
                    'linkedin' => 'LinkedIn',
                    'instagram' => 'Instagram',
                    'facebook' => 'Facebook',
                    'twitter' => 'Twitter/X',
                    'tiktok' => 'TikTok',
                    'youtube' => 'YouTube',
                    default => $contenu->plateforme,
                };
                
                $typeLabel = match($contenu->type) {
                    'post' => 'Post',
                    'story' => 'Story',
                    'reel' => 'Reel',
                    'article' => 'Article',
                    'video' => 'Vidéo',
                    'carousel' => 'Carousel',
                    default => $contenu->type,
                };
                
                return [
                    'type' => 'contenu',
                    'id' => $contenu->id,
                    'titre' => $contenu->titre ?: 'Sans titre',
                    'sous_titre' => "{$typeLabel} - {$plateformeLabel}",
                    'action' => 'Publier le contenu',
                    'date' => $contenu->date_publication_planifiee,
                    'url' => ContenuResource::getUrl('view', ['record' => $contenu]),
                ];
            });
        
        return $prospects->concat($contenus)
            ->sortBy('date')
            ->take(8)
            ->values();
    }
}
