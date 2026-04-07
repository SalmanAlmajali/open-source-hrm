<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas - {{ $periodLabel }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #0ea5e9;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .company-name { font-size: 18px; font-weight: bold; color: #0ea5e9; }
        .doc-title    { font-size: 14px; font-weight: bold; margin-top: 2px; }
        .doc-meta     { font-size: 10px; color: #64748b; margin-top: 2px; }

        /* Summary cards */
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 8px 0;
        }
        .summary-cell { display: table-cell; width: 25%; }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
        }
        .card-label  { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; font-weight: bold; }
        .card-value  { font-size: 14px; font-weight: bold; margin-top: 2px; }
        .card-sub    { font-size: 9px; color: #94a3b8; margin-top: 2px; }
        .card-green  { border-left: 4px solid #10b981; }
        .card-red    { border-left: 4px solid #ef4444; }
        .card-blue   { border-left: 4px solid #3b82f6; }
        .card-gray   { border-left: 4px solid #94a3b8; }
        .text-green  { color: #059669; }
        .text-red    { color: #dc2626; }
        .text-blue   { color: #2563eb; }

        /* Formula row */
        .formula {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 20px;
            font-size: 11px;
            font-family: monospace;
        }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th {
            background: #0ea5e9;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        thead th.right { text-align: right; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        tbody td.right  { text-align: right; }
        tbody td.mono   { font-family: monospace; font-size: 9px; color: #94a3b8; }
        tbody td.green  { color: #059669; font-weight: bold; }
        tbody td.red    { color: #dc2626; font-weight: bold; }
        tbody td.blue   { color: #2563eb; font-weight: bold; }
        tfoot td { padding: 7px 8px; font-weight: bold; border-top: 2px solid #cbd5e1; font-size: 11px; }
        tfoot td.right  { text-align: right; }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 99px;
            font-size: 9px;
            color: #fff;
            font-weight: bold;
        }
        .auto-badge { font-size: 9px; color: #f59e0b; }

        /* Category breakdown */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0ea5e9;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }
        .cat-row { display: table; width: 100%; padding: 3px 0; }
        .cat-dot  { display: table-cell; width: 10px; vertical-align: middle; }
        .cat-dot span { display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
        .cat-name { display: table-cell; padding-left: 4px; font-size: 10px; }
        .cat-val  { display: table-cell; text-align: right; font-size: 10px; font-weight: bold; }

        /* Footer */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">HRMS Rajakon</div>
            <div class="doc-title">Laporan Arus Kas</div>
            <div class="doc-meta">
                Rekening: {{ $account?->name ?? 'Semua' }} &nbsp;|&nbsp; Periode: {{ $periodLabel }}
            </div>
        </div>
        <div class="header-right">
            <div class="doc-meta">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-cell">
            <div class="card card-gray">
                <div class="card-label">Saldo Awal</div>
                <div class="card-value">{{ 'Rp ' . number_format($openingBalance, 0, ',', '.') }}</div>
                <div class="card-sub">Sebelum {{ $periodLabel }}</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="card card-green">
                <div class="card-label">⬆ Pemasukan</div>
                <div class="card-value text-green">{{ 'Rp ' . number_format($periodInflow, 0, ',', '.') }}</div>
                <div class="card-sub">Periode {{ $periodLabel }}</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="card card-red">
                <div class="card-label">⬇ Pengeluaran</div>
                <div class="card-value text-red">{{ 'Rp ' . number_format($periodOutflow, 0, ',', '.') }}</div>
                <div class="card-sub">Periode {{ $periodLabel }}</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="card {{ $closingBalance >= 0 ? 'card-blue' : 'card-red' }}">
                <div class="card-label">Saldo Akhir</div>
                <div class="card-value {{ $closingBalance >= 0 ? 'text-blue' : 'text-red' }}">
                    {{ 'Rp ' . number_format($closingBalance, 0, ',', '.') }}
                </div>
                <div class="card-sub">{{ $account?->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Formula --}}
    <div class="formula">
        Rp {{ number_format($openingBalance, 0, ',', '.') }}
        &nbsp;+&nbsp; <span class="text-green">Rp {{ number_format($periodInflow, 0, ',', '.') }}</span>
        &nbsp;−&nbsp; <span class="text-red">Rp {{ number_format($periodOutflow, 0, ',', '.') }}</span>
        &nbsp;=&nbsp;
        <strong class="{{ $closingBalance >= 0 ? 'text-blue' : 'text-red' }}">
            Rp {{ number_format($closingBalance, 0, ',', '.') }}
        </strong>
    </div>

    {{-- Transactions Table --}}
    <div class="section-title">Detail Transaksi — {{ $periodLabel }}</div>
    @if ($transactions->isEmpty())
        <p style="color:#94a3b8; font-size:10px; text-align:center; padding:16px 0;">Tidak ada transaksi pada periode ini.</p>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:70px">Tanggal</th>
                <th style="width:90px">No. Ref</th>
                <th>Keterangan</th>
                <th style="width:100px">Kategori</th>
                <th class="right" style="width:100px">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $trx)
            <tr>
                <td>{{ $trx->transaction_date->format('d/m/Y') }}</td>
                <td class="mono">{{ $trx->reference_number ?? '-' }}</td>
                <td>
                    {{ $trx->description ?? '-' }}
                    @if ($trx->is_auto_generated)
                        <span class="auto-badge">⚡ Auto</span>
                    @endif
                </td>
                <td>
                    @if ($trx->category)
                        <span class="badge" style="background-color:{{ $trx->category->color ?? '#94a3b8' }}">
                            {{ $trx->category->name }}
                        </span>
                    @elseif ($trx->type === 'transfer')
                        <span class="badge" style="background-color:#3b82f6">Transfer</span>
                    @else
                        -
                    @endif
                </td>
                <td @class(['right', 'green' => $trx->type === 'inflow', 'red' => $trx->type === 'outflow', 'blue' => $trx->type === 'transfer'])>
                    {{ $trx->type === 'inflow' ? '+' : '-' }}
                    Rp {{ number_format((float)$trx->amount, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="">Net Arus Kas Periode</td>
                <td class="right {{ ($periodInflow - $periodOutflow) >= 0 ? 'green' : 'red' }}">
                    Rp {{ number_format($periodInflow - $periodOutflow, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Category Breakdown --}}
    @if (!$categoryBreakdown->isEmpty())
    <div class="section-title">Rincian per Kategori</div>
    @foreach ($categoryBreakdown->sortByDesc('total') as $cat)
    <div class="cat-row">
        <div class="cat-dot"><span style="background-color:{{ $cat['color'] }}"></span></div>
        <div class="cat-name">{{ $cat['name'] }} ({{ $cat['count'] }}x)</div>
        <div class="cat-val {{ $cat['type'] === 'inflow' ? 'text-green' : 'text-red' }}">
            Rp {{ number_format((float)$cat['total'], 0, ',', '.') }}
        </div>
    </div>
    @endforeach
    @endif

    {{-- Footer --}}
    <div class="footer">
        Laporan ini dibuat otomatis oleh sistem HRMS Rajakon &nbsp;|&nbsp;
        {{ $periodLabel }} &nbsp;|&nbsp; {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
