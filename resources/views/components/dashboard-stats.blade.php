@props(['user'])

@php
    $totalBookings = $user->isAdmin() 
        ? \App\Models\Booking::count() 
        : $user->bookings()->count();

    $upcomingSessions = $user->isAdmin()
        ? \App\Models\Booking::where('booking_date', '>=', now()->toDateString())->where('status', 'confirmed')->count()
        : $user->bookings()->where('booking_date', '>=', now()->toDateString())->where('status', 'confirmed')->count();

    $totalRevenue = $user->isAdmin()
        ? \App\Models\Booking::where('status', 'confirmed')->sum('total_price')
        : $user->bookings()->where('status', 'confirmed')->sum('total_price');
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stat 1 -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ $user->isAdmin() ? 'Global Bookings' : 'Total Bookings' }}
            </p>
            <p class="text-3xl font-black text-gray-900 mt-2">{{ $totalBookings }}</p>
            <span class="text-xs text-emerald-600 font-medium inline-block mt-1">Live recorded sessions</span>
        </div>
        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-lg">
            #
        </div>
    </div>

    <!-- Stat 2 -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Upcoming Sessions</p>
            <p class="text-3xl font-black text-gray-900 mt-2">{{ $upcomingSessions }}</p>
            <span class="text-xs text-indigo-600 font-medium inline-block mt-1">Scheduled & Confirmed</span>
        </div>
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-lg">
            📅
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ $user->isAdmin() ? 'Gross Platform Revenue' : 'Total Investment' }}
            </p>
            <p class="text-3xl font-black text-gray-900 mt-2">Rs. {{ number_format($totalRevenue, 2) }}</p>
            <span class="text-xs text-gray-400 font-medium inline-block mt-1">Processed transactions</span>
        </div>
        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-bold text-lg">
            Rs
        </div>
    </div>
</div>