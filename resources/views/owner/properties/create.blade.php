<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Property') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.properties.index') }}" class="text-sm text-gray-600">&larr; {{ __('Cancel') }}</a>
            <h1 class="text-2xl font-semibold mt-2 mb-4">{{ __('Add New Property') }}</h1>

            <div class="bg-white rounded-lg shadow">
                <div class="p-4">
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                        <p>{{ __('This form is a placeholder. Connect it to the store controller to save the actual data.') }}</p>
                    </div>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Property Name') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="{{ __('Example: Harmony Kost') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="2" placeholder="{{ __('Full address') }}"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Latitude') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="-6.200000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Longitude') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="106.816666">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Rules') }}</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" placeholder="{{ __('Write down the rules or provisions of the kost') }}"></textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button class="bg-blue-500 text-white px-4 py-2 rounded" type="button" disabled>{{ __('Save (coming soon)') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
