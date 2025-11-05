<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back to user list') }}</a>
            <div class="flex justify-between items-start mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ $user->name }}</h1>
                    <p class="text-gray-600 mb-0">{{ $user->email }}</p>
                </div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">{{ $user->role }}</span>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('User Information') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Name') }}</dt>
                            <dd class="text-gray-900">{{ $user->name }}</dd>
                            <dt class="text-gray-500">{{ __('Email') }}</dt>
                            <dd class="text-gray-900">{{ $user->email }}</dd>
                            <dt class="text-gray-500">{{ __('Role') }}</dt>
                            <dd class="text-gray-900 capitalize">{{ $user->role }}</dd>
                            <dt class="text-gray-500">{{ __('Joined') }}</dt>
                            <dd class="text-gray-900">{{ optional($user->created_at)->format('d M Y') }}</dd>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Actions') }}</h3>
                        {{-- Add actions here, e.g., change role, suspend user --}}
                        <button class="bg-red-500 text-white px-4 py-2 rounded-md">{{ __('Suspend User') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>