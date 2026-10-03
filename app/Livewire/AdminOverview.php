<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;

class AdminOverview extends Component
{
    use WithPagination;

    public $message = '';

    public function toggleFacility(int $facilityId)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        $facility = Facility::findOrFail($facilityId);
        $facility->update(['is_active' => ! $facility->is_active]);

        $this->message = "{$facility->name} status changed to " . ($facility->is_active ? 'Active' : 'Disabled');
    }

    public function updateBookingStatus(int $bookingId, string $newStatus)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        $booking = Booking::findOrFail($bookingId);
        $booking->update(['status' => $newStatus]);

        $this->message = "Booking #{$booking->id} updated to {$newStatus}.";
    }

    public function render()
    {
        return view('livewire.admin-overview', [
            'facilities' => Facility::withCount('bookings')->get(),
            'allBookings' => Booking::with(['user', 'facility'])->latest()->paginate(8),
        ]);
    }
}