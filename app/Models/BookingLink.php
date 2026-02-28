<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BookingLink extends Model
{
    protected $fillable = [
        'prospect_id',
        'booking_form_id',
        'token',
        'nom',
        'description',
        'date_expiration',
        'actif',
    ];

    protected $casts = [
        'date_expiration' => 'datetime',
        'actif' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bookingLink) {
            if (empty($bookingLink->token)) {
                $bookingLink->token = Str::random(32);
            }
        });
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function bookingForm(): BelongsTo
    {
        return $this->belongsTo(BookingForm::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getUrlAttribute(): string
    {
        return route('booking.show', ['token' => $this->token]);
    }

    public function isExpired(): bool
    {
        if (!$this->date_expiration) {
            return false;
        }
        return now()->greaterThan($this->date_expiration);
    }

    public function isValid(): bool
    {
        return $this->actif && !$this->isExpired();
    }
}
