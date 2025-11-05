<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Property Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.properties.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back to property list') }}</a>
            <div class="flex justify-between items-start mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ $property->name }}</h1>
                    <p class="text-gray-600 mb-0">{{ $property->address }}</p>
                </div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">{{ $property->status }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow mb-4">
                        <div class="p-4 border-b flex justify-between items-center">
                            <h2 class="text-lg font-semibold mb-0">{{ __('Room Types & Units') }}</h2>
                            <a href="{{ route('owner.room-types.index') }}" class="text-blue-500">{{ __('Manage Types') }}</a>
                        </div>
                        <div class="p-4">
                            @forelse ($property->roomTypes as $roomType)
                                <div class="border rounded p-3 mb-3">
                                    <div class="flex justify-between items-start gap-3">
                                        <div>
                                            <h3 class="text-lg font-semibold mb-1">{{ $roomType->name }}</h3>
                                            <p class="text-sm text-gray-600 mb-0">
                                                {{ __('Area') }} {{ $roomType->area_m2 ?? '-' }} m² · {{ __('Bathroom') }} {{ $roomType->bathroom_type ?? '-' }}
                                            </p>
                                        </div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}/{{ __('month') }}</span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-3">
                                        @forelse ($roomType->rooms as $room)
                                            <div class="border rounded p-2 h-full">
                                                <p class="font-semibold mb-1">{{ __('Room') }} {{ $room->room_code }}</p>
                                                <p class="text-sm text-gray-600 mb-1">{{ __('Status') }}: {{ ucfirst($room->status) }}</p>
                                                <p class="text-sm text-gray-600 mb-0">
                                                    {{ __('Last contract') }}:
                                                    {{ optional($room->contracts->first())->start_date?->format('d M Y') ?? '-' }}
                                                </p>
                                            </div>
                                        @empty
                                            <div class="col-span-full">
                                                <p class="text-sm text-gray-500">{{ __('No rooms for this type.') }}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-600 mb-0">{{ __('No room types yet. Add them through the room types menu.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b">
                            <h2 class="text-lg font-semibold mb-0">{{ __('Rules') }}</h2>
                        </div>
                        <div class="p-4">
                            <p class="text-gray-600 mb-0">{{ $property->rules_text ? nl2br(e($property->rules_text)) : __('No specific rules yet.') }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-white rounded-lg shadow mb-4">
                        <div class="p-4 border-b flex justify-between items-center">
                            <h2 class="text-lg font-semibold mb-0">{{ __('Recent Tasks') }}</h2>
                            <a href="{{ route('owner.shared-tasks.index') }}" class="text-blue-500">{{ __('View All') }}</a>
                        </div>
                        <div class="p-4">
                            <ul class="divide-y">
                                @forelse ($property->sharedTasks as $task)
                                    <li class="py-2">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold mb-1">{{ $task->title }}</p>
                                                <p class="text-sm text-gray-600 mb-0">{{ optional($task->next_run_at)->format('d M Y') ?? __('Flexible schedule') }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-gray-600">{{ __('No tasks for this property.') }}</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4 border-b">
                            <h2 class="text-lg font-semibold mb-0">{{ __('Quick Actions') }}</h2>
                        </div>
                        <div class="p-4 grid gap-2">
                            <a href="{{ route('owner.properties.edit', $property) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-center">{{ __('Edit Property') }}</a>
                            <a href="{{ route('owner.room-types.create') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-center">{{ __('Add Room Type') }}</a>
                            <a href="{{ route('owner.rooms.create') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-center">{{ __('Add Room') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
