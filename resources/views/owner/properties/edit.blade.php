<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Property') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.properties.show', $property) }}" class="text-sm text-gray-600">&larr; {{ __('Back') }}</a>
            <h1 class="text-2xl font-semibold mt-2 mb-4">{{ __('Edit Property') }}</h1>

            <div class="bg-white rounded-lg shadow">
                <div class="p-4">
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                        <p>{{ __('Property data updates will be available after the storage endpoint is implemented.') }}</p>
                    </div>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Property Name') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" value="{{ $property->name }}" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" disabled>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" {{ $property->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" rows="2" disabled>{{ $property->address }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Latitude') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" value="{{ $property->lat }}" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Longitude') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" value="{{ $property->lng }}" disabled>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Rules') }}</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-100" rows="4" disabled>{{ $property->rules_text }}</textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
