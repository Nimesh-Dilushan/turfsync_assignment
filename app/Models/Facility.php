<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sport_type',
        'hourly_rate',
        'description',
        'is_active',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active facilities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * A facility has many slots.
     */
    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    /**
     * A facility has many bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}