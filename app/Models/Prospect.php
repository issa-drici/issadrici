<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Prospect extends Model
{
    use LogsActivity;
    protected $fillable = [
        'prenom', 'nom', 'fonction', 'societe',
        'secteur', 'localisation', 'taille_estimee', 'type_entreprise',
        'linkedin', 'email', 'telephone',
        'observations', 'signal_declencheur', 'hypotheses_organisationnelles', 'points_friction_probables',
        'statut', 'canal_principal', 'prochaine_action', 'date_prochaine_action', 'niveau_interet',
        'budget_estime', 'douleur', 'valeur_perdue_actuelle', 'valeur_deal',
        'montant_gagne', 'montant_perdu', 'montant_proposition', 'duree_proposition',
    ];

    protected $casts = [
        'date_prochaine_action' => 'date',
        'budget_estime' => 'decimal:2',
        'valeur_perdue_actuelle' => 'decimal:2',
        'valeur_deal' => 'decimal:2',
        'montant_gagne' => 'decimal:2',
        'montant_perdu' => 'decimal:2',
        'montant_proposition' => 'decimal:2',
    ];

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    public function opportunites(): HasMany
    {
        return $this->hasMany(Opportunite::class);
    }

    public function preparationsMessages(): HasMany
    {
        return $this->hasMany(PreparationMessage::class);
    }

    public function propositions(): HasMany
    {
        return $this->hasMany(Proposition::class);
    }

    public function bookingLinks(): HasMany
    {
        return $this->hasMany(\App\Models\BookingLink::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(\App\Models\Booking::class);
    }

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getDerniereInteractionAttribute()
    {
        return $this->interactions()->latest('date')->first();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
