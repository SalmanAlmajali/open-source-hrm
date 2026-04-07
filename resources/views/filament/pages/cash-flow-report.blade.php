<x-filament-panels::page>
    {{-- Filter Form --}}
    <x-filament::section>
        <form wire:submit.prevent="null">
            {{ $this->form }}
        </form>
    </x-filament::section>

    @php
    $data = $this->getReportData();
    $account = $data['account'];
    $openingBalance = $data['openingBalance'];
    $periodInflow = $data['periodInflow'];
    $periodOutflow = $data['periodOutflow'];
    $closingBalance = $data['closingBalance'];
    $transactions = $data['transactions'];
    $breakdown = $data['categoryBreakdown'];
    $period = $this->getPeriodLabel();
    @endphp

    @if (!$account)
    <x-filament::section>
        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
            <x-heroicon-o-banknotes class="w-16 h-16 mb-4 opacity-40" />
            <p class="text-lg font-medium">Belum ada rekening kas aktif</p>
            <p class="text-sm mt-1">Buat rekening kas terlebih dahulu di menu <strong>Rekening Kas</strong>.</p>
        </div>
    </x-filament::section>
    @else

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Opening Balance --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Saldo Awal</p>
            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-gray-100">
                {{ $this->formatRupiah($openingBalance) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Sebelum periode {{ $period }}</p>
        </div>

        {{-- Period Inflow --}}
        <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
                ⬆ Total Pemasukan
            </p>
            <p class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300">
                {{ $this->formatRupiah($periodInflow) }}
            </p>
            <p class="text-xs text-emerald-500 mt-1">Periode {{ $period }}</p>
        </div>

        {{-- Period Outflow --}}
        <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-600 dark:text-red-400">
                ⬇ Total Pengeluaran
            </p>
            <p class="mt-1 text-2xl font-bold text-red-700 dark:text-red-300">
                {{ $this->formatRupiah($periodOutflow) }}
            </p>
            <p class="text-xs text-red-500 mt-1">Periode {{ $period }}</p>
        </div>

        {{-- Closing Balance --}}
        <div @class([ 'rounded-xl border p-5 shadow-sm' , 'border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20'=> $closingBalance >= 0,
            'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20' => $closingBalance < 0,
                ])>
                <p @class([ 'text-xs font-semibold uppercase tracking-wide' , 'text-blue-600 dark:text-blue-400'=> $closingBalance >= 0,
                    'text-red-600 dark:text-red-400' => $closingBalance < 0,
                        ])>Saldo Akhir</p>
                <p @class([ 'mt-1 text-2xl font-bold' , 'text-blue-700 dark:text-blue-300'=> $closingBalance >= 0,
                    'text-red-700 dark:text-red-300' => $closingBalance < 0,
                        ])>{{ $this->formatRupiah($closingBalance) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $account->name }} · {{ $period }}</p>
        </div>
    </div>

    {{-- Formula Row --}}
    <x-filament::section>
        <div class="flex flex-wrap items-center gap-2 text-sm font-mono text-gray-600 dark:text-gray-300">
            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">{{ $this->formatRupiah($openingBalance) }}</span>
            <span class="text-emerald-600 font-bold">+ {{ $this->formatRupiah($periodInflow) }}</span>
            <span class="text-red-500 font-bold">− {{ $this->formatRupiah($periodOutflow) }}</span>
            <span class="text-gray-400">=</span>
            <span @class([ 'px-3 py-1 rounded font-bold' , 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300'=> $closingBalance >= 0,
                'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' => $closingBalance < 0,
                    ])>{{ $this->formatRupiah($closingBalance) }}</span>
        </div>
    </x-filament::section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Transaction Table --}}
        <div class="lg:col-span-2">
            <x-filament::section :heading="'Transaksi Periode ' . $period" icon="heroicon-o-list-bullet">
                @if ($transactions->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <x-heroicon-o-inbox class="w-10 h-10 mx-auto mb-2 opacity-40" />
                    <p>Tidak ada transaksi pada periode ini.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs text-gray-500 uppercase tracking-wide">
                                <th class="pb-2 pr-4">Tanggal</th>
                                <th class="pb-2 pr-4">No. Ref</th>
                                <th class="pb-2 pr-4">Keterangan</th>
                                <th class="pb-2 pr-4">Kategori</th>
                                <th class="pb-2 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($transactions as $trx)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-2 pr-4 text-gray-500 whitespace-nowrap">
                                    {{ $trx->transaction_date->format('d/m/Y') }}
                                </td>
                                <td class="py-2 pr-4 font-mono text-xs text-gray-400">
                                    {{ $trx->reference_number ?? '-' }}
                                </td>
                                <td class="py-2 pr-4 max-w-xs">
                                    <span class="truncate block">{{ $trx->description ?? '-' }}</span>
                                    @if ($trx->is_auto_generated)
                                    <span class="text-xs text-amber-500">⚡ Auto</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-4">
                                    @if ($trx->category)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium text-white"
                                        style="background-color: {{ $trx->category->color ?? '#94a3b8' }}">
                                        {{ $trx->category->name }}
                                    </span>
                                    @elseif ($trx->type === 'transfer')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                        ⇄ Transfer
                                    </span>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td @class([ 'py-2 text-right font-semibold whitespace-nowrap' , 'text-emerald-600 dark:text-emerald-400'=> $trx->type === 'inflow',
                                    'text-red-500 dark:text-red-400' => $trx->type === 'outflow',
                                    'text-blue-500 dark:text-blue-400' => $trx->type === 'transfer',
                                    ])>
                                    {{ $trx->type === 'inflow' ? '+' : '-' }}
                                    {{ 'Rp ' . number_format((float)$trx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-semibold">
                                <td colspan="4" class="pt-2 text-gray-600 dark:text-gray-300">Net Arus Kas</td>
                                <td @class([ 'pt-2 text-right' , 'text-emerald-600 dark:text-emerald-400'=> ($periodInflow - $periodOutflow) >= 0,
                                    'text-red-500 dark:text-red-400' => ($periodInflow - $periodOutflow) < 0,
                                        ])>
                                        {{ $this->formatRupiah($periodInflow - $periodOutflow) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </x-filament::section>
        </div>

        {{-- Category Breakdown --}}
        <div>
            <x-filament::section heading="Rincian per Kategori" icon="heroicon-o-tag">
                @if ($breakdown->isEmpty())
                <p class="text-gray-400 text-sm text-center py-4">Tidak ada data kategori.</p>
                @else
                <div class="space-y-3">
                    @foreach ($breakdown->sortByDesc('total') as $cat)
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                style="background-color: {{ $cat['color'] }}"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300 truncate">
                                {{ $cat['name'] }}
                            </span>
                            <span class="text-xs text-gray-400">({{ $cat['count'] }}x)</span>
                        </div>
                        <span @class([ 'text-sm font-semibold whitespace-nowrap' , 'text-emerald-600 dark:text-emerald-400'=> $cat['type'] === 'inflow',
                            'text-red-500 dark:text-red-400' => $cat['type'] === 'outflow',
                            ])>
                            {{ 'Rp ' . number_format((float)$cat['total'], 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </x-filament::section>
        </div>

    </div>

    @endif

</x-filament-panels::page>