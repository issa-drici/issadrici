<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'booking_link_id',
        'prospect_id',
        'user_id',
        'date_choisie',
        'donnees_formulaire',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_choisie' => 'datetime',
        'donnees_formulaire' => 'array',
    ];

    public function bookingLink(): BelongsTo
    {
        return $this->belongsTo(BookingLink::class);
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
