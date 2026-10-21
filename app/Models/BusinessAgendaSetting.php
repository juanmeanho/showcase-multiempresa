<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessAgendaSetting extends Model
{
    protected $fillable = [
        'business_id',
        'slot_interval_minutes',
        'opening_time',
        'closing_time',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
