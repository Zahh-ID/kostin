<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoice Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('tenant.invoices.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back to invoice list') }}</a>
            <div class="flex items-center justify-between mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('Invoice Details') }}</h1>
                    <p class="text-gray-600 mb-0">{{ __('Invoice for') }} {{ $invoice->period_month }}/{{ $invoice->period_year }}</p>
                </div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }} capitalize">
                    {{ $invoice->status }}
                </span>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Property Information') }}</h3>
                        <p class="font-semibold mb-1">{{ $invoice->contract?->room?->roomType?->property?->name ?? '-' }}</p>
                        <p class="text-gray-600 text-sm mb-3">{{ $invoice->contract?->room?->roomType?->property?->address ?? '-' }}</p>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Room') }}</dt>
                            <dd class="text-gray-900">{{ $invoice->contract?->room?->room_code ?? '-' }}</dd>
                            <dt class="text-gray-500">{{ __('Room Type') }}</dt>
                            <dd class="text-gray-900">{{ $invoice->contract?->room?->roomType?->name ?? '-' }}</dd>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Invoice Summary') }}</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Due Date') }}</dt>
                            <dd class="text-gray-900">{{ optional($invoice->due_date)->format('d M Y') ?? '-' }}</dd>
                            <dt class="text-gray-500">{{ __('Total Amount') }}</dt>
                            <dd class="text-gray-900">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</dd>
                            <dt class="text-gray-500">{{ __('Status') }}</dt>
                            <dd class="text-gray-900 capitalize">{{ $invoice->status }}</dd>
                        </dl>
                        @if ($invoice->status !== 'paid')
                            <div class="mt-4">
                                <button class="bg-indigo-600 text-white px-4 py-2 rounded-md">{{ __('Pay Now with QRIS') }}</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>