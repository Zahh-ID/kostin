<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contract Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('tenant.contracts.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back to contract list') }}</a>
            @php
                $nextCoverageLabel = optional($nextCoverageStart)->translatedFormat('F Y');
            @endphp
            @if ($errors->any())
                <div class="mt-3 mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="flex items-center justify-between mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('Contract Details') }}</h1>
                    <p class="text-gray-600 mb-0">
                        {{ __('Contract number #') }}{{ $contract->id }} {{ __('for') }} {{ $contract->room->roomType->property->name ?? '-' }}<br>
                        {{ __('Berakhir pada:') }}
                        {{ optional($contract->end_date)->translatedFormat('d M Y') ?? __('Tidak ditentukan') }}
                    </p>
                    @if (! is_null($daysToEnd) && $daysToEnd <= 30)
                        <p class="text-red-600 text-sm mt-1">
                            {{ __('Kontrak akan berakhir dalam :days hari. Segera perpanjang dengan membayar invoice baru.', ['days' => max($daysToEnd, 0)]) }}
                        </p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('tenant.contracts.pdf', $contract) }}" class="bg-white border px-3 py-2 rounded text-sm text-gray-700" target="_blank" rel="noopener">
                        {{ __('Unduh PDF') }}
                    </a>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">{{ $contract->status }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg shadow h-full">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold mb-0">{{ __('Property Information') }}</h2>
                    </div>
                    <div class="p-4">
                        <p class="font-semibold mb-1">{{ $contract->room->roomType->property->name ?? '-' }}</p>
                        <p class="text-gray-600 text-sm mb-3">{{ $contract->room->roomType->property->address ?? '-' }}</p>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Room') }}</dt>
                            <dd class="text-gray-900">{{ $contract->room->room_code ?? '-' }}</dd>
                            <dt class="text-gray-500">{{ __('Type') }}</dt>
                            <dd class="text-gray-900">{{ $contract->room->roomType->name ?? '-' }}</dd>
                            <dt class="text-gray-500">{{ __('Price/month') }}</dt>
                            <dd class="text-gray-900">Rp{{ number_format($contract->price_per_month ?? 0, 0, ',', '.') }}</dd>
                            <dt class="text-gray-500">{{ __('Deposit') }}</dt>
                            <dd class="text-gray-900">Rp{{ number_format($contract->deposit_amount ?? 0, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow h-full">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold mb-0">{{ __('Contract Terms') }}</h2>
                    </div>
                    <div class="p-4">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Start Date') }}</dt>
                            <dd class="text-gray-900">{{ optional($contract->start_date)->format('d M Y') }}</dd>
                            <dt class="text-gray-500">{{ __('End Date') }}</dt>
                            <dd class="text-gray-900">{{ optional($contract->end_date)->format('d M Y') ?? __('Ongoing') }}</dd>
                            <dt class="text-gray-500">{{ __('Due Date') }}</dt>
                            <dd class="text-gray-900">{{ __('Date') }} {{ $contract->billing_day }}</dd>
                            <dt class="text-gray-500">{{ __('Grace Days') }}</dt>
                            <dd class="text-gray-900">{{ $contract->grace_days }} {{ __('days') }}</dd>
                            <dt class="text-gray-500">{{ __('Late Fee') }}</dt>
                            <dd class="text-gray-900">Rp{{ number_format($contract->late_fee_per_day ?? 0, 0, ',', '.') }}/{{ __('day') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            @if ($contract->status === 'active')
                <div class="bg-white rounded-lg shadow mt-4">
                    <div class="p-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold mb-0">{{ __('Buat Invoice Pembayaran') }}</h2>
                            <p class="text-gray-600 text-sm mb-0">
                                {{ __('Periode berikutnya: :period', ['period' => $nextCoverageLabel ?? __('-')]) }}
                            </p>
                        </div>
                    </div>
                    <div class="p-4">
                        <form method="POST" action="{{ route('tenant.contracts.invoices.store', $contract) }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                            @csrf
                            <div class="flex flex-column gap-2">
                                <label class="text-sm text-gray-600">{{ __('Jumlah Bulan') }}</label>
                                <select name="months_count" class="border rounded px-3 py-2 text-sm">
                                    @foreach (range(1, 12) as $monthOption)
                                        <option value="{{ $monthOption }}" @selected(old('months_count', 1) == $monthOption)>{{ $monthOption }} {{ __('bulan') }}</option>
                                    @endforeach
                                </select>
                                @error('months_count')
                                    <div class="text-red-500 text-xs">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="flex-grow text-sm text-gray-600 md:mt-6">
                                {{ __('Tagihan dibuat per transaksi dan akan memperpanjang masa sewa sesuai jumlah bulan yang dipilih. Invoice baru hanya dapat dibuat setelah invoice sebelumnya lunas.') }}
                            </div>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm h-fit md:mt-6">
                                {{ __('Buat Invoice') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow mt-4">
                <div class="p-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold mb-0">{{ __('Pengakhiran Kontrak') }}</h2>
                        <p class="text-gray-600 text-sm mb-0">
                            {{ __('Ajukan terminasi jika Anda berencana pindah sebelum kontrak berakhir.') }}
                        </p>
                    </div>
                    @if ($contract->status === 'active' && (! $latestTerminationRequest || $latestTerminationRequest->status !== 'pending'))
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#terminateModal">
                            {{ __('Ajukan Pengakhiran') }}
                        </button>
                    @endif
                </div>
                <div class="p-4">
                    @if ($latestTerminationRequest)
                        <div class="mb-0">
                            <p class="mb-1"><strong>{{ __('Tanggal yang diminta:') }}</strong> {{ optional($latestTerminationRequest->requested_end_date)->translatedFormat('d M Y') }}</p>
                            <p class="mb-1"><strong>{{ __('Status:') }}</strong> {{ ucfirst($latestTerminationRequest->status) }}</p>
                            <p class="mb-0"><strong>{{ __('Alasan:') }}</strong> {{ $latestTerminationRequest->reason ?: __('Tidak ada alasan tambahan.') }}</p>
                            @if ($latestTerminationRequest->owner_notes)
                                <p class="mb-0 mt-2"><strong>{{ __('Catatan pemilik:') }}</strong> {{ $latestTerminationRequest->owner_notes }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('Belum ada permintaan pengakhiran untuk kontrak ini.') }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mt-4">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold mb-0">{{ __('Billing History') }}</h2>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Period') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Due Date') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Total') }}
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">{{ __('Actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($contract->invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $invoice->months_count ?? 1 }} {{ __('bln') }}<br>
                                            <span class="text-gray-500 text-xs">
                                                {{ optional($invoice->coverage_start_month ? \Illuminate\Support\Carbon::create($invoice->coverage_start_year, $invoice->coverage_start_month, 1) : null)?->translatedFormat('M Y') }}
                                                -
                                                {{ optional($invoice->coverage_end_month ? \Illuminate\Support\Carbon::create($invoice->coverage_end_year, $invoice->coverage_end_month, 1) : null)?->translatedFormat('M Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($invoice->due_date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }} capitalize">
                                                {{ $invoice->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('tenant.invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Details') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ __('No invoices for this contract.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="modal fade" id="terminateModal" tabindex="-1" aria-labelledby="terminateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('tenant.contracts.termination.store', $contract) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="terminateModalLabel">{{ __('Ajukan Pengakhiran Kontrak') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Tanggal pindah / berhenti') }}</label>
                        <input type="date" class="form-control" name="requested_end_date" value="{{ old('requested_end_date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Alasan (opsional)') }}</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="{{ __('Ceritakan alasan pengakhiran') }}">{{ old('reason') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Kirim Permintaan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
