<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shared Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('Shared Tasks') }}</h1>
                    <p class="text-gray-600 mb-0">{{ __('Manage shared tasks for your properties.') }}</p>
                </div>
                <a href="{{ route('owner.shared-tasks.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">{{ __('Create New Task') }}</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Title') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Property') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Next Run') }}
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">{{ __('Actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($sharedTasks as $task)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $task->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->property->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($task->next_run_at)->format('d M Y') ?? __('Flexible schedule') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('owner.shared-tasks.show', $task) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Details') }}</a>
                                                <a href="{{ route('owner.shared-tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ __('No shared tasks found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($sharedTasks instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="p-6">
                        {{ $sharedTasks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>