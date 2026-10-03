<div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
    @if ($successMessage)
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
            {{ $successMessage }}
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold">
            {{ $errorMessage }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Facility Selector -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Select Arena / Turf</label>
            <select wire:model.live="selectedFacilityId" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @foreach ($facilities as $facility)
                    <option value="{{ $facility->id }}">{{ $facility->name }} (Rs. {{ number_format($facility->hourly_rate, 2) }}/hr)</option>
                @endforeach
            </select>
        </div>

        <!-- Date Picker -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Select Date</label>
            <input type="date" wire:model.live="selectedDate" min="{{ now()->toDateString() }}" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>
    </div>

    <!-- Available Slots Grid -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Available Time Slots</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @forelse ($slots as $slot)
                @php
                    $isBooked = in_array($slot->start_time, $bookedSlotTimes);
                    $isSelected = $selectedSlotId == $slot->id;
                @endphp

                <button
                    type="button"
                    wire:click="$set('selectedSlotId', {{ $slot->id }})"
                    @disabled($isBooked)
                    class="py-3 px-4 rounded-xl text-xs font-semibold tracking-wide border transition-all text-center
                        {{ $isBooked
                            ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed line-through'
                            : ($isSelected
                                ? 'bg-indigo-600 text-white border-indigo-600 shadow-md ring-2 ring-indigo-400'
                                : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100') }}">
                    {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }}
                    <span class="block text-[10px] mt-0.5 font-normal">
                        {{ $isBooked ? 'Booked' : ($isSelected ? 'Selected' : 'Available') }}
                    </span>
                </button>
            @empty
                <p class="text-sm text-gray-500 col-span-full">No operating slots configured for this facility.</p>
            @endforelse
        </div>
        @error('selectedSlotId') <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <!-- Add-ons Selection -->
    <div class="mb-6 border-t border-gray-100 pt-4">
        <label class="block text-sm font-semibold text-gray-700 mb-3">Add Equipment & Services</label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach ($addons as $addon)
                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-gray-300 cursor-pointer text-sm">
                    <input type="checkbox" wire:model.live="selectedAddons" value="{{ $addon->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <div class="flex-1">
                        <span class="text-gray-900 font-medium block">{{ $addon->name }}</span>
                        <span class="text-gray-500 text-xs">+Rs. {{ number_format($addon->price_per_session, 2) }}</span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Booking Summary & Action -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-xl bg-gray-50 border border-gray-200">
        <div>
            <span class="text-xs uppercase font-bold text-gray-400 block tracking-wider">Total Amount</span>
            <span class="text-2xl font-black text-gray-900">Rs. {{ number_format($totalPrice, 2) }}</span>
        </div>

        <button
            type="button"
            wire:click="bookSlot"
            @disabled(!$selectedSlotId)
            class="w-full sm:w-auto px-8 py-3 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
            Confirm Reservation
        </button>
    </div>
</div>