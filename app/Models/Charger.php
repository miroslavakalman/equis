<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Charger extends Model
{
    protected $fillable = [
        'name', 'max_power', 'status', 'transformer_id'
    ];

    public function transformer(): BelongsTo
    {
        return $this->belongsTo(Transformer::class);
    }

    public function chargingSession(): HasMany
    {
        return $this->hasMany(ChargingSession::class);
    }

    public function activeSession()
    {
        return $this->chargingSession()->whereNull('ended_at')->first();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'free';
    }
}