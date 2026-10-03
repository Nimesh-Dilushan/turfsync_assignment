<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Admin can view any booking; customers can only view their own.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    /**
     * Admin or the booking owner can cancel.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }
}