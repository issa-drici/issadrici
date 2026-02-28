<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingForm extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'champs',
        'creneaux_disponibles',
        'duree_call',
        'actif',
    ];

    protected $casts = [
        'champs' => 'array',
        'creneaux_disponibles' => 'array',
        'actif' => 'boolean',
    ];

    public function bookingLinks(): HasMany
    {
        return $this->hasMany(BookingLink::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
