<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Shared Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.shared-tasks.show', $sharedTask) }}" class="text-sm text-gray-600">&larr; {{ __('Back') }}</a>
            <h1 class="text-2xl font-semibold mt-2 mb-4">{{ __('Edit Shared Task') }}</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Title') }}</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $sharedTask->title }}" placeholder="{{ __('e.g., Clean common area, Check fire extinguisher') }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" placeholder="{{ __('Provide a detailed description of the task.') }}">{{ $sharedTask->description }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Property') }}</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($properties as $property)
                                    <option value="{{ $property->id }}" {{ $sharedTask->property_id === $property->id ? 'selected' : '' }}>{{ $property->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Assignee') }}</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($assignees as $assignee)
                                    <option value="{{ $assignee->id }}" {{ $sharedTask->assignee_user_id === $assignee->id ? 'selected' : '' }}>{{ $assignee->name }} ({{ $assignee->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Next Run At') }}</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ optional($sharedTask->next_run_at)->format('Y-m-d') }}">
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