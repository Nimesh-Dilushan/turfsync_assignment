<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Facility;
use App\Models\Slot;
use App\Models\Addon;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingManager extends Component
{
    public $facilities;
    public $addons;
    public $selectedFacilityId;
    public $selectedDate;
    public $selectedSlotId;
    public $selectedAddons = []; // array of addon IDs

    public $totalPrice = 0.00;
    public $successMessage = '';
    public $errorMessage = '';

    public function mount()
    {
        $this->facilities = Facility::active()->get();
        $this->addons = Addon::all();
        $this->selectedFacilityId = $this->facilities->first()?->id;
        $this->selectedDate = now()->addDay()->toDateString();
        $this->calculateTotal();
    }

    public function updated($propertyName)
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $facility = Facility::find($this->selectedFacilityId);
        $baseRate = $facility ? (float) $facility->hourly_rate : 0.00;

        $addonsTotal = Addon::whereIn('id', $this->selectedAddons)
            ->sum('price_per_session');

        $this->totalPrice = $baseRate + (float) $addonsTotal;
    }

    public function bookSlot()
    {
        $this->validate([
            'selectedFacilityId' => 'required|exists:facilities,id',
            'selectedDate'       => 'required|date|after_or_equal:today',
            'selectedSlotId'     => 'required|exists:slots,id',
        ]);

        $slot = Slot::findOrFail($this->selectedSlotId);

        // Check slot conflict
        $isBooked = Booking::where('facility_id', $this->selectedFacilityId)
            ->where('booking_date', $this->selectedDate)
            ->where('start_time', $slot->start_time)
            ->where('status', 'confirmed')
            ->exists();

        if ($isBooked) {
            $this->errorMessage = 'This slot has already been reserved for the selected date.';
            $this->successMessage = '';
            return;
        }

        // Create booking record using Eloquent
        $booking = Booking::create([
            'user_id'       => Auth::id(),
            'facility_id'   => $this->selectedFacilityId,
            'booking_date'  => $this->selectedDate,
            'start_time'    => $slot->start_time,
            'end_time'      => $slot->end_time,
            'total_price'   => $this->totalPrice,
            'status'        => 'confirmed',
        ]);

        // Attach addons via pivot
        if (!empty($this->selectedAddons)) {
            $booking->addons()->attach($this->selectedAddons, ['quantity' => 1]);
        }

        $this->selectedSlotId = null;
        $this->errorMessage = '';
        $this->successMessage = 'Booking confirmed successfully!';
    }

    public function render()
    {
        $slots = [];
        $bookedSlotTimes = [];

        if ($this->selectedFacilityId && $this->selectedDate) {
            $slots = Slot::where('facility_id', $this->selectedFacilityId)->get();

            $bookedSlotTimes = Booking::where('facility_id', $this->selectedFacilityId)
                ->where('booking_date', $this->selectedDate)
                ->where('status', 'confirmed')
                ->pluck('start_time')
                ->toArray();
        }

        return view('livewire.booking-manager', [
            'slots' => $slots,
            'bookedSlotTimes' => $bookedSlotTimes,
        ]);
    }
}