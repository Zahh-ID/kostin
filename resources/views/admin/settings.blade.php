<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold mb-3">{{ __('System Settings') }}</h1>
            <p class="text-gray-600 mb-4">{{ __('Manage global system configurations.') }}</p>

            <div class="bg-white rounded-lg shadow p-6">
                <form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Midtrans Server Key') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ env('MIDTRANS_SERVER_KEY') }}" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Midtrans Client Key') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ env('MIDTRANS_CLIENT_KEY') }}" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Email Sender') }}</label>
                            <input type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ env('MAIL_FROM_ADDRESS') }}" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('App Name') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ env('APP_NAME') }}" disabled>
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