<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Room Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.rooms.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back') }}</a>
            <div class="flex justify-between items-start mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('Room') }} {{ $room->room_code }}</h1>
                    <p class="text-gray-600 mb-0">{{ $room->roomType?->property?->name }} &middot; {{ $room->roomType?->name }}</p>
                </div>
                <a href="{{ route('owner.rooms.edit', $room) }}" class="bg-blue-500 text-white px-4 py-2 rounded">{{ __('Edit') }}</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg shadow h-full">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold mb-0">{{ __('Room Information') }}</h2>
                    </div>
                    <div class="p-4">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Status') }}</dt>
                            <dd class="text-gray-900 capitalize">{{ $room->status }}</dd>
                            <dt class="text-gray-500">{{ __('Custom Price') }}</dt>
                            <dd class="text-gray-900">{{ $room->custom_price ? 'Rp'.number_format($room->custom_price, 0, ',', '.') : __('Follow type price') }}</dd>
                            <dt class="text-gray-500">{{ __('Additional Facilities') }}</dt>
                            <dd class="text-gray-900">{{ collect($room->facilities_override_json)->implode(', ') ?: '-' }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow h-full">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold mb-0">{{ __('Last Contract') }}</h2>
                    </div>
                    <div class="p-4">
                        @forelse ($room->contracts as $contract)
                            <div class="border rounded p-3 mb-3">
                                <p class="font-semibold mb-1">{{ __('Tenant') }}: {{ $contract->tenant?->name ?? '-' }}</p>
                                <p class="text-sm text-gray-600 mb-1">
                                    {{ __('Period') }}: {{ optional($contract->start_date)->format('d M Y') }} -
                                    {{ optional($contract->end_date)->format('d M Y') ?? __('Ongoing') }}
                                </p>
                                <p class="text-sm text-gray-600 mb-0">{{ __('Status') }}: {{ ucfirst($contract->status) }}</p>
                            </div>
                        @empty
                            <p class="text-gray-600 mb-0">{{ __('No contract history for this room.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
