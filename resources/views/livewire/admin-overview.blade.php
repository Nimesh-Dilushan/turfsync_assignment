<div class="space-y-8">
    @if ($message)
        <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-sm font-semibold">
            {{ $message }}
        </div>
    @endif

    <!-- Facilities Control Grid -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Facility Management</h3>
        <p class="text-xs text-gray-500 mb-6">Enable or disable turf availability in real time</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($facilities as $facility)
                <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-gray-900 block text-sm">{{ $facility->name }}</span>
                        <span class="text-xs text-gray-500">{{ $facility->sport_type }} • Rs. {{ number_format($facility->hourly_rate, 2) }}/hr</span>
                        <span class="text-[11px] text-indigo-600 block mt-1 font-medium">{{ $facility->bookings_count }} bookings registered</span>
                    </div>

                    <button
                        type="button"
                        wire:click="toggleFacility({{ $facility->id }})"
                        class="px-4 py-2 text-xs font-semibold rounded-xl transition shadow-sm {{ $facility->is_active ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                        {{ $facility->is_active ? 'Active' : 'Disabled' }}
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Platform Wide Bookings Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-1">System Master Bookings</h3>
        <p class="text-xs text-gray-500 mb-6">Review, manage, and override reservation statuses across all users</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-500 font-semibold border-y border-gray-100">
                    <tr>
                        <th class="py-3 px-4">Ref</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Facility</th>
                        <th class="py-3 px-4">Schedule</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Current Status</th>
                        <th class="py-3 px-4 text-right">Admin Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($allBookings as $booking)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-medium text-xs text-gray-800">
                                #TRF-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-gray-900 block text-xs">{{ $booking->user->name }}</span>
                                <span class="text-[11px] text-gray-400">{{ $booking->user->email }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-medium text-gray-800">
                                {{ $booking->facility->name }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-gray-600">
                                {{ $booking->booking_date->format('M d, Y') }}<br>
                                <span class="text-[11px] text-gray-400">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-bold text-gray-900">
                                Rs. {{ number_format($booking->total_price, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $booking->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($booking->status === 'cancelled' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-gray-100 text-gray-600') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                @if ($booking->status !== 'completed')
                                    <button
                                        type="button"
                                        wire:click="updateBookingStatus({{ $booking->id }}, 'completed')"
                                        class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                                        Complete
                                    </button>
                                @endif
                                @if ($booking->status !== 'cancelled')
                                    <button
                                        type="button"
                                        wire:click="updateBookingStatus({{ $booking->id }}, 'cancelled')"
                                        class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                        Cancel
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $allBookings->links() }}
        </div>
    </div>
</div>