<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Contracts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('My Contracts') }}</h1>
                    <p class="text-gray-600 mb-0">
                        {{ $withHistory ? __('Menampilkan seluruh riwayat kontrak Anda.') : __('Menampilkan kontrak aktif yang sedang berjalan.') }}
                    </p>
                </div>
                <a href="{{ $withHistory ? route('tenant.contracts.index') : route('tenant.contracts.index', ['history' => 1]) }}"
                   class="text-sm text-indigo-600 hover:text-indigo-800 mt-2 md:mt-0">
                    {{ $withHistory ? __('Tampilkan kontrak aktif saja') : __('Lihat riwayat kontrak') }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Property') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Room') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Period') }}
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
                                @forelse ($contracts as $contract)
                                    @php
                                        $room = $contract->room;
                                        $roomType = $room?->roomType;
                                        $property = $roomType?->property;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $property?->name ?? __('Property') }}</div>
                                            <div class="text-sm text-gray-500">{{ $property?->address ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $room?->room_code }}</div>
                                            <div class="text-sm text-gray-500">{{ $roomType?->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ optional($contract->start_date)->format('d M Y') }} -
                                            {{ optional($contract->end_date)->format('d M Y') ?? __('Ongoing') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                                                {{ $contract->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('tenant.contracts.show', $contract) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Details') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ $withHistory ? __('Belum ada riwayat kontrak.') : __('Belum ada kontrak aktif. Ajukan sewa melalui halaman properti.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($contracts instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="p-6">
                        {{ $contracts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
