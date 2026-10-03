<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'start_time',
        'end_time',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Accessor to format time range (e.g., "06:00 - 07:00").
     */
    protected function formattedTime(): Attribute
    {
        return Attribute::make(
            get: fn () => substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5),
        );
    }
}