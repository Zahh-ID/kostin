<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shared Task Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.shared-tasks.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back to shared tasks list') }}</a>
            <div class="flex justify-between items-start mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ $sharedTask->title }}</h1>
                    <p class="text-gray-600 mb-0">{{ $sharedTask->property->name ?? '-' }}</p>
                </div>
                <a href="{{ route('owner.shared-tasks.edit', $sharedTask) }}" class="bg-blue-500 text-white px-4 py-2 rounded">{{ __('Edit') }}</a>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Task Information') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Description') }}</dt>
                            <dd class="text-gray-900">{{ $sharedTask->description }}</dd>
                            <dt class="text-gray-500">{{ __('Property') }}</dt>
                            <dd class="text-gray-900">{{ $sharedTask->property->name ?? '-' }}</dd>
                            <dt class="text-gray-500">{{ __('Assignee') }}</dt>
                            <dd class="text-gray-900">{{ $sharedTask->assignee->name ?? '-' }}</dd>
                            <dt class="text-gray-500">{{ __('Next Run At') }}</dt>
                            <dd class="text-gray-900">{{ optional($sharedTask->next_run_at)->format('d M Y') ?? __('Flexible schedule') }}</dd>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Task Logs') }}</h3>
                        <ul class="list-disc list-inside text-sm text-gray-600">
                            @forelse ($sharedTask->logs as $log)
                                <li>{{ $log->description }} - {{ optional($log->created_at)->format('d M Y H:i') }}</li>
                            @empty
                                <li>{{ __('No logs for this task.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>