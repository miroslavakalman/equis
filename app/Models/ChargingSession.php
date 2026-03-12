<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChargingSession extends Model
{
    protected $table = 'charging_sessions';

    protected $fillable = [
        'charger_id', 'user_id', 'power', 'mode', 'started_at', 'ended_at' 
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function charger(): BelongsTo
    {
        return $this->belongsTo(Charger::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function isActive(): bool
    {
        return is_null($this->ended_at);
    }
}