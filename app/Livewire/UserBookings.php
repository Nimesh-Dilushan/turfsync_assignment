<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class UserBookings extends Component
{
    use WithPagination;

    public $statusFilter = 'all'; // 'all', 'confirmed', 'cancelled'
    public $feedbackMessage = '';

    public function cancelBooking(int $bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status === 'cancelled') {
            return;
        }

        $booking->update(['status' => 'cancelled']);
        $this->feedbackMessage = 'Booking #' . $booking->id . ' has been cancelled successfully.';
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Booking::with(['facility', 'addons'])
            ->where('user_id', Auth::id())
            ->latest('booking_date');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.user-bookings', [
            'bookings' => $query->paginate(6),
        ]);
    }
}