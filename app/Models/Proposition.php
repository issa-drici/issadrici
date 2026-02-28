<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposition extends Model
{
    protected $fillable = [
        'prospect_id', 'montant', 'duree', 'date_envoi', 
        'statut', 'description', 'notes',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_envoi' => 'date',
    ];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }
}
