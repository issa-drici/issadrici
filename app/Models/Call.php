<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Call extends Model
{
    use LogsActivity;
    protected $fillable = [
        'prospect_id', 'booking_id', 'date_planifiee', 'objectif_call', 'points_a_verifier',
        'date_realisee', 'resultat', 'statut', 'prochaine_etape', 'notes',
    ];

    protected $casts = [
        'date_planifiee' => 'datetime',
        'date_realisee' => 'datetime',
    ];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Booking::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
