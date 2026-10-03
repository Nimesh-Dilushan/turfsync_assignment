<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price_per_session',
    ];

    protected $casts = [
        'price_per_session' => 'decimal:2',
    ];

    /**
     * An addon can belong to multiple bookings.
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_addon')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}