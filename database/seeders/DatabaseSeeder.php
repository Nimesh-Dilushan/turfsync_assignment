<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Facility;
use App\Models\Slot;
use App\Models\Addon;
use App\Models\Booking;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Admin & Customer)
        $admin = User::firstOrCreate(
            ['email' => 'admin@turfsync.com'],
            [
                'name' => 'TurfSync Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@turfsync.com'],
            [
                'name' => 'Nimesh Sasikumar',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]
        );

        // 2. Seed Facilities
        $cricketTurf = Facility::create([
            'name' => 'Emerald Indoor Cricket Arena',
            'sport_type' => 'Cricket',
            'hourly_rate' => 3500.00,
            'description' => 'Full-length indoor cricket pitch with high-grade synthetic turf and bowling machine compatibility.',
            'is_active' => true,
        ]);

        $futsalTurf = Facility::create([
            'name' => 'Apex Futsal Ground',
            'sport_type' => 'Futsal',
            'hourly_rate' => 4500.00,
            'description' => '5-a-side rubber infill turf with rebound boards and tournament standard floodlighting.',
            'is_active' => true,
        ]);

        // 3. Seed Operating Slots for Facilities (1-hour slots from 06:00 to 22:00)
        $facilities = [$cricketTurf, $futsalTurf];
        $timeSlots = [
            ['06:00:00', '07:00:00'],
            ['07:00:00', '08:00:00'],
            ['08:00:00', '09:00:00'],
            ['16:00:00', '17:00:00'],
            ['17:00:00', '18:00:00'],
            ['18:00:00', '19:00:00'],
            ['19:00:00', '20:00:00'],
            ['20:00:00', '21:00:00'],
            ['21:00:00', '22:00:00'],
        ];

        foreach ($facilities as $facility) {
            foreach ($timeSlots as $slot) {
                Slot::create([
                    'facility_id' => $facility->id,
                    'start_time' => $slot[0],
                    'end_time' => $slot[1],
                ]);
            }
        }

        // 4. Seed Add-ons
        $balls = Addon::create([
            'name' => 'Match Leather Cricket Balls (Set of 2)',
            'price_per_session' => 600.00,
        ]);

        $bibs = Addon::create([
            'name' => 'Training Bibs & Markers Set',
            'price_per_session' => 400.00,
        ]);

        $floodlights = Addon::create([
            'name' => 'High-Intensity Floodlights Surcharge',
            'price_per_session' => 800.00,
        ]);

        // 5. Seed a Sample Booking with Pivot Addons
        $booking = Booking::create([
            'user_id' => $customer->id,
            'facility_id' => $cricketTurf->id,
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
            'total_price' => 4100.00, // 3500 base + 600 balls
            'status' => 'confirmed',
        ]);

        $booking->addons()->attach($balls->id, ['quantity' => 1]);
    }
}