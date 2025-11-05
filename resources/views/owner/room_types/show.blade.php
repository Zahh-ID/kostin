<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Room Type Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.room-types.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back to room types list') }}</a>
            <div class="flex justify-between items-start mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ $roomType->name }}</h1>
                    <p class="text-gray-600 mb-0">{{ $roomType->property->name ?? '-' }}</p>
                </div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">{{ __('Base Price') }}: Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}</span>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Room Type Information') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Area (m²)') }}</dt>
                            <dd class="text-gray-900">{{ $roomType->area_m2 ?? '-' }}</dd>
                            <dt class="text-gray-500">{{ __('Bathroom Type') }}</dt>
                            <dd class="text-gray-900">{{ ucfirst($roomType->bathroom_type ?? '-') }}</dd>
                            <dt class="text-gray-500">{{ __('Deposit') }}</dt>
                            <dd class="text-gray-900">Rp{{ number_format($roomType->deposit ?? 0, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Facilities') }}</h3>
                        <ul class="list-disc list-inside text-sm text-gray-600">
                            @forelse (collect($roomType->facilities_json) as $facility)
                                <li>{{ $facility }}</li>
                            @empty
                                <li>{{ __('No facilities listed.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mt-4">
                <div class="p-4 border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold mb-0">{{ __('Rooms in this Type') }}</h2>
                    <a href="{{ route('owner.room-types.rooms.create', $roomType) }}" class="bg-blue-500 text-white px-4 py-2 rounded">{{ __('Add Room') }}</a>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Room Code') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Custom Price') }}
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">{{ __('Actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($roomType->rooms as $room)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $room->room_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 capitalize">
                                                {{ $room->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $room->custom_price ? 'Rp'.number_format($room->custom_price, 0, ',', '.') : __('Follow base price') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('owner.rooms.edit', $room) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ __('No rooms found for this type.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>