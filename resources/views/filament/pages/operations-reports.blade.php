<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-4">
        <x-filament::section><x-slot name="heading">Bookings</x-slot><div class="text-3xl font-bold">{{ $bookings }}</div></x-filament::section>
        <x-filament::section><x-slot name="heading">Completed Jobs</x-slot><div class="text-3xl font-bold">{{ $completedJobs }}</div></x-filament::section>
        <x-filament::section><x-slot name="heading">Avg Inspection</x-slot><div class="text-3xl font-bold">{{ $avgInspection }}%</div></x-filament::section>
        <x-filament::section><x-slot name="heading">Outstanding</x-slot><div class="text-3xl font-bold">{{ number_format($outstanding, 2) }}</div></x-filament::section>
    </div>
</x-filament-panels::page>
