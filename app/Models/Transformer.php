<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transformer extends Model
{
    protected $fillable = [
        'name', 'address', 'status', 'capacity', 'current_load', 'temperature'
    ];

    public function chargers(): HasMany
    {
        return $this->hasMany(Charger::class);   
    }

    public function loadPercentage(): float
    {
        if ($this->capacity == 0) return 0;
        return round(($this->current_load / $this->capacity) * 100, 1);
    }

    public function statusColor(): string
    {
        $percent = $this->loadPercentage();
        if ($percent >= 85) return 'red';
        if ($percent >= 70) return 'yellow';
        return 'green';
    }
}