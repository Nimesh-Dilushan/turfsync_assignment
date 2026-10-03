<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-900 leading-tight">
                {{ auth()->user()->isAdmin() ? __('Admin Operations Command') : __('Turf & Facility Reservation') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide {{ auth()->user()->isAdmin() ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800' }}">
                {{ strtoupper(auth()->user()->role) }} ACCOUNT
            </span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 1. Universal Stats Banner -->
            <x-dashboard-stats :user="auth()->user()" />

            @if (auth()->user()->isAdmin())
                <!-- 2. Admin Command Center -->
                <livewire:admin-overview />
            @else
                <!-- 3. Customer Booking Experience -->
                <div class="space-y-8">
                    <livewire:booking-manager />
                    <livewire:user-bookings />
                </div>
            @endif
        </div>
    </div>
</x-app-layout>