<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Opportunite extends Model
{
    use LogsActivity;
    protected $fillable = [
        'prospect_id', 'stade', 'montant_estime', 'probabilite',
        'date_estimee_decision', 'description', 'notes',
    ];

    protected $casts = [
        'montant_estime' => 'decimal:2',
        'date_estimee_decision' => 'date',
    ];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
