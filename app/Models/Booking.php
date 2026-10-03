<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_id',
        'booking_date',
        'start_time',
        'end_time',
        'total_price',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'booking_addon')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Scope for confirmed bookings.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for upcoming bookings.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('booking_date', '>=', now()->toDateString())
                     ->orderBy('booking_date', 'asc')
                     ->orderBy('start_time', 'asc');
    }
}