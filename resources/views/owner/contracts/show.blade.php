<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tenant Contract Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('owner.contracts.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back') }}</a>
            <div class="flex justify-between items-start mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('Tenant Contract') }}</h1>
                    <p class="text-gray-600 mb-0">
                        {{ $contract->tenant?->name }} &middot;
                        {{ $contract->room?->roomType?->property?->name }}
                    </p>
                </div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">{{ $contract->status }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg shadow h-full">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold mb-0">{{ __('Contract Details') }}</h2>
                    </div>
                    <div class="p-4">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Tenant') }}</dt>
                            <dd class="text-gray-900">
                                {{ $contract->tenant?->name }}<br>
                                <span class="text-gray-600">{{ $contract->tenant?->email }}</span>
                            </dd>
                            <dt class="text-gray-500">{{ __('Period') }}</dt>
                            <dd class="text-gray-900">
                                {{ optional($contract->start_date)->format('d M Y') }} -
                                {{ optional($contract->end_date)->format('d M Y') ?? __('Ongoing') }}
                            </dd>
                            <dt class="text-gray-500">{{ __('Price') }}</dt>
                            <dd class="text-gray-900">Rp{{ number_format($contract->price_per_month ?? 0, 0, ',', '.') }}/{{ __('month') }}</dd>
                            <dt class="text-gray-500">{{ __('Deposit') }}</dt>
                            <dd class="text-gray-900">Rp{{ number_format($contract->deposit_amount ?? 0, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow h-full">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold mb-0">{{ __('Payment Terms') }}</h2>
                    </div>
                    <div class="p-4">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">{{ __('Due Date') }}</dt>
                            <dd class="text-gray-900">{{ __('Date') }} {{ $contract->billing_day }}</dd>
                            <dt class="text-gray-500">{{ __('Grace Days') }}</dt>
                            <dd class="text-gray-900">{{ $contract->grace_days }} {{ __('days') }}</dd>
                            <dt class="text-gray-500">{{ __('Daily Fine') }}</dt>
                            <dd class="text-gray-900">Rp{{ number_format($contract->late_fee_per_day ?? 0, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mt-4">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold mb-0">{{ __('Invoice History') }}</h2>
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
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($contract->invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->period_month }}/{{ $invoice->period_year }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($invoice->due_date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }} capitalize">
                                                {{ $invoice->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
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
