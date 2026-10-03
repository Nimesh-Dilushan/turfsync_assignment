<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\Slot;
use App\Models\Addon;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\BookingResource;

class BookingApiController extends Controller
{
    // GET /api/v1/facilities
    public function facilities()
    {
        $facilities = Facility::active()->with('slots')->get();
        return FacilityResource::collection($facilities);
    }

    // GET /api/v1/my-bookings
    public function myBookings(Request $request)
    {
        $bookings = Booking::with(['facility', 'addons'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    // POST /api/v1/bookings
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id'  => 'required|exists:facilities,id',
            'slot_id'      => 'required|exists:slots,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'addon_ids'    => 'nullable|array',
            'addon_ids.*'  => 'exists:addons,id',
        ]);

        $slot = Slot::findOrFail($validated['slot_id']);
        $facility = Facility::findOrFail($validated['facility_id']);

        // Check conflicts
        $conflict = Booking::where('facility_id', $facility->id)
            ->where('booking_date', $validated['booking_date'])
            ->where('start_time', $slot->start_time)
            ->where('status', 'confirmed')
            ->exists();

        if ($conflict) {
            return response()->json([
                'status' => 'conflict',
                'message' => 'The selected slot is already booked for this date.',
            ], 409);
        }

        // Calculate total
        $addonsTotal = !empty($validated['addon_ids'])
            ? Addon::whereIn('id', $validated['addon_ids'])->sum('price_per_session')
            : 0;

        $totalPrice = $facility->hourly_rate + $addonsTotal;

        $booking = Booking::create([
            'user_id'      => $request->user()->id,
            'facility_id'  => $facility->id,
            'booking_date' => $validated['booking_date'],
            'start_time'   => $slot->start_time,
            'end_time'     => $slot->end_time,
            'total_price'  => $totalPrice,
            'status'       => 'confirmed',
        ]);

        if (!empty($validated['addon_ids'])) {
            $booking->addons()->attach($validated['addon_ids'], ['quantity' => 1]);
        }

        $booking->load(['facility', 'addons']);

        return (new BookingResource($booking))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/v1/weather-check (External API integration criteria)
    public function weatherCheck(Request $request)
    {
        $city = $request->query('city', 'Colombo');

        // Consumes free Open-Meteo API (no API key required)
        $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => 6.9271,  // Colombo coordinates
            'longitude' => 79.8612,
            'current' => 'temperature_2m,relative_humidity_2m,precipitation,weather_code',
        ]);

        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'location' => $city,
                'forecast' => $response->json()['current'] ?? [],
            ]);
        }

        return response()->json([
            'status' => 'unavailable',
            'message' => 'Weather service temporarily unreachable.',
        ], 503);
    }
}