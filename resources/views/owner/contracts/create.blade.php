<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Contract') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.contracts.index') }}" class="text-sm text-gray-600">&larr; {{ __('Cancel') }}</a>
            <h1 class="text-2xl font-semibold mt-2 mb-4">{{ __('Create Contract') }}</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Tenant') }}</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Room') }}</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->room_code }} ({{ $room->roomType->property->name }} - {{ $room->roomType->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('End Date (Optional)') }}</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Price Per Month') }}</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="1500000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Deposit Amount') }}</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="500000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Billing Day') }}</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Grace Days') }}</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Late Fee Per Day') }}</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="10000">
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