<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Room Type') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.room-types.show', $roomType) }}" class="text-sm text-gray-600">&larr; {{ __('Back') }}</a>
            <h1 class="text-2xl font-semibold mt-2 mb-4">{{ __('Edit Room Type') }}</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Property') }}</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" disabled>
                                <option value="{{ $roomType->property->id }}">{{ $roomType->property->name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $roomType->name }}" placeholder="{{ __('e.g., Single AC, Private Bathroom') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Area (m²)') }}</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $roomType->area_m2 }}" placeholder="12">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Bathroom Type') }}</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="private" {{ $roomType->bathroom_type === 'private' ? 'selected' : '' }}>{{ __('Private') }}</option>
                                <option value="shared" {{ $roomType->bathroom_type === 'shared' ? 'selected' : '' }}>{{ __('Shared') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Base Price') }}</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $roomType->base_price }}" placeholder="1500000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Deposit') }}</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $roomType->deposit }}" placeholder="500000">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Facilities') }}</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" placeholder="{{ __('e.g., AC, Wi-Fi, Wardrobe') }}">{{ collect($roomType->facilities_json)->implode(', ') }}</textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button class="bg-blue-500 text-white px-4 py-2 rounded" type="button" disabled>{{ __('Save (coming soon)') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>