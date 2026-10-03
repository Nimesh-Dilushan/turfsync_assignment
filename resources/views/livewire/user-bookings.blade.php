<div class="mt-10 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-gray-100 gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900">My Reservations</h3>
            <p class="text-xs text-gray-500 mt-0.5">Manage your upcoming and past turf bookings</p>
        </div>

        <!-- Filter Tabs -->
        <div class="inline-flex p-1 bg-gray-100 rounded-xl">
            <button
                type="button"
                wire:click="$set('statusFilter', 'all')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'all' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
                All
            </button>
            <button
                type="button"
                wire:click="$set('statusFilter', 'confirmed')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'confirmed' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
                Active
            </button>
            <button
                type="button"
                wire:click="$set('statusFilter', 'cancelled')"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'cancelled' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
                Cancelled
            </button>
        </div>
    </div>

    @if ($feedbackMessage)
        <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium">
            {{ $feedbackMessage }}
        </div>
    @endif

    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-500 font-semibold border-y border-gray-100">
                <tr>
                    <th class="py-3 px-4">Booking Ref</th>
                    <th class="py-3 px-4">Facility / Sport</th>
                    <th class="py-3 px-4">Date & Slot</th>
                    <th class="py-3 px-4">Add-ons</th>
                    <th class="py-3 px-4">Total</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($bookings as $item)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3.5 px-4 font-mono font-medium text-xs text-gray-800">
                            #TRF-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-gray-900 block">{{ $item->facility->name }}</span>
                            <span class="text-[11px] text-gray-400 font-medium">{{ $item->facility->sport_type }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-xs">
                            <span class="font-medium text-gray-800 block">{{ $item->booking_date->format('M d, Y') }}</span>
                            <span class="text-gray-400">{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-xs">
                            @if ($item->addons->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($item->addons as $addon)
                                        <span class="inline-flex px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-[10px] font-medium">
                                            {{ $addon->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">None</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-gray-900 text-xs">
                            Rs. {{ number_format($item->total_price, 2) }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if ($item->status === 'confirmed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Confirmed
                                </span>
                            @elseif ($item->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    Cancelled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ ucfirst($item->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            @if ($item->status === 'confirmed' && $item->booking_date->isFuture())
                                <button
                                    type="button"
                                    wire:click="cancelBooking({{ $item->id }})"
                                    wire:confirm="Are you sure you want to cancel this reservation?"
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">
                                    Cancel
                                </button>
                            @else
                                <span class="text-xs text-gray-300 select-none">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-sm text-gray-400">
                            No reservations found matching the selected filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>