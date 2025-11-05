<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Room') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.rooms.show', $room) }}" class="text-sm text-gray-600">&larr; {{ __('Back') }}</a>
            <h1 class="text-2xl font-semibold mt-2 mb-4">{{ __('Edit Room') }} {{ $room->room_code }}</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p>{{ __('To enable this form, add an update route that saves the changes.') }}</p>
                </div>
                <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Room Type') }}</label>
                        <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" disabled>
                            @foreach ($roomTypes as $roomType)
                                <option value="{{ $roomType->id }}" {{ $room->room_type_id === $roomType->id ? 'selected' : '' }}>
                                    {{ $roomType->name }} — {{ $roomType->property?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Room Code') }}</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" value="{{ $room->room_code }}" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" value="{{ ucfirst($room->status) }}" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Custom Price') }}</label>
                        <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" value="{{ $room->custom_price }}" disabled>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Override Facilities (JSON)') }}</label>
                        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" rows="3" disabled>{{ json_encode($room->facilities_override_json) }}</textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
