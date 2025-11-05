<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Room Types') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('Room Types') }}</h1>
                    <p class="text-gray-600 mb-0">{{ __('Manage your room types and their details.') }}</p>
                </div>
                <a href="{{ route('owner.room-types.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">{{ __('Add Room Type') }}</a>
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
                                        {{ __('Property') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Area (m²)') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Base Price') }}
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">{{ __('Actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($roomTypes as $roomType)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $roomType->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $roomType->property->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $roomType->area_m2 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp{{ number_format($roomType->base_price ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('owner.room-types.show', $roomType) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Details') }}</a>
                                                <a href="{{ route('owner.room-types.edit', $roomType) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ __('No room types found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($roomTypes instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="p-6">
                        {{ $roomTypes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>