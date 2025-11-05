<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notification Settings') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <x-section-title>
                    <x-slot name="title">{{ __('Notification Preferences') }}</x-slot>
                    <x-slot name="description">{{ __('Manage your communication channel preferences.') }}</x-slot>
                </x-section-title>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form>
                        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="notifEmail" class="flex items-center">
                                        <input id="notifEmail" type="checkbox" class="form-checkbox" {{ $preferences['email'] ? 'checked' : '' }} disabled>
                                        <span class="ml-2 text-sm text-gray-600">{{ __('Email reminders for bills & contracts') }}</span>
                                    </label>
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label for="notifWhatsapp" class="flex items-center">
                                        <input id="notifWhatsapp" type="checkbox" class="form-checkbox" {{ $preferences['whatsapp'] ? 'checked' : '' }} disabled>
                                        <span class="ml-2 text-sm text-gray-600">{{ __('WhatsApp Notifications') }}</span>
                                    </label>
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <x-label for="language" value="{{ __('Language') }}" />
                                    <x-input id="language" type="text" class="mt-1 block w-full bg-gray-100" value="{{ strtoupper($preferences['language'] ?? 'id') }}" readonly />
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <x-label for="timezone" value="{{ __('Timezone') }}" />
                                    <x-input id="timezone" type="text" class="mt-1 block w-full bg-gray-100" value="{{ $preferences['timezone'] }}" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                                <p>{{ __('These settings are initial display only. Integrate with a notification preferences table for full functionality.') }}</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
