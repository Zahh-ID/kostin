<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Properties') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('My Properties') }}</h1>
                    <p class="text-gray-600 mb-0">{{ __('Manage all your owned kost branches.') }}</p>
                </div>
                <a href="{{ route('owner.properties.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">{{ __('Add Property') }}</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Address') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Room Types') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Rooms') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">{{ __('Actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($properties as $property)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $property->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $property->address }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $property->roomTypes->count() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $property->roomTypes->flatMap->rooms->count() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                                                {{ $property->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('owner.properties.show', $property) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Details') }}</a>
                                                <a href="{{ route('owner.properties.edit', $property) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ __('No properties registered yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($properties instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="p-6">
                        {{ $properties->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
