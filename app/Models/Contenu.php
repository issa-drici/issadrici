<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenu extends Model
{
    protected $fillable = [
        'type',
        'titre',
        'angle',
        'cible',
        'probleme_vise',
        'solution_proposee',
        'objectif_contenu',
        'call_to_action',
        'contenu',
        'plateforme',
        'statut',
        'date_publication_planifiee',
        'date_publication_reelle',
        'url_publication',
        'tags',
        'notes',
        'image_url',
        'engagement_estime',
    ];

    protected $casts = [
        'date_publication_planifiee' => 'datetime',
        'date_publication_reelle' => 'datetime',
        'tags' => 'array',
        'engagement_estime' => 'integer',
    ];

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'post' => 'Post',
            'story' => 'Story',
            'reel' => 'Reel',
            'article' => 'Article',
            'video' => 'Vidéo',
            'carousel' => 'Carousel',
            default => $this->type,
        };
    }

    public function getPlateformeLabelAttribute(): string
    {
        return match ($this->plateforme) {
            'linkedin' => 'LinkedIn',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter/X',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            default => $this->plateforme,
        };
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'planifie' => 'Planifié',
            'publie' => 'Publié',
            'archive' => 'Archivé',
            default => $this->statut,
        };
    }
}
