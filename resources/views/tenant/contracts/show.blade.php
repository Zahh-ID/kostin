<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contract Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('tenant.contracts.index') }}" class="text-sm text-gray-600">&larr; {{ __('Back to contract list') }}</a>
            <div class="flex items-center justify-between mt-2 mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-1">{{ __('Contract Details') }}</h1>
                    <p class="text-gray-600 mb-0">{{ __('Contract number #') }}{{ $contract->id }} {{ __('for') }} {{ $contract->room->roomType->property->name ?? '-' }}</p>
                </div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">{{ $contract->status }}</span>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->period_month }}/{{ $invoice->period_year }}</td>
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
