<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CashFlowReportController extends Controller
{
    // ── Shared data builder ───────────────────────────────────────────────────

    private function buildReportData(Request $request): array
    {
        $accountId = $request->query('account_id');
        $year      = (int) $request->query('year', date('Y'));
        $month     = $request->query('month') ? (int) $request->query('month') : null;

        $account = $accountId ? CashAccount::find($accountId) : CashAccount::where('is_active', true)->first();

        if (!$account) {
            return compact('year', 'month') + [
                'account'           => null,
                'openingBalance'    => 0,
                'periodInflow'      => 0,
                'periodOutflow'     => 0,
                'closingBalance'    => 0,
                'transactions'      => collect(),
                'categoryBreakdown' => collect(),
                'periodLabel'       => $this->periodLabel($year, $month),
            ];
        }

        // Opening balance — everything before the period
        $beforeDate = $month
            ? Carbon::create($year, $month, 1)->startOfMonth()
            : Carbon::create($year, 1, 1)->startOfYear();

        $beforeIn    = (float) $account->transactions()->where('type', 'inflow')
            ->where('transaction_date', '<', $beforeDate)->sum('amount');
        $transferIn  = (float) CashTransaction::where('transfer_to_account_id', $account->id)
            ->where('type', 'transfer')->where('transaction_date', '<', $beforeDate)->sum('amount');
        $beforeOut   = (float) $account->transactions()->whereIn('type', ['outflow', 'transfer'])
            ->where('transaction_date', '<', $beforeDate)->sum('amount');

        $openingBalance = (float) $account->opening_balance + $beforeIn + $transferIn - $beforeOut;

        // Period transactions
        $pq = $account->transactions()
            ->when($month, fn($q) => $q->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month))
            ->when(!$month, fn($q) => $q->whereYear('transaction_date', $year));

        $periodInflow      = (float) (clone $pq)->where('type', 'inflow')->sum('amount');
        $periodOutflow     = (float) (clone $pq)->where('type', 'outflow')->sum('amount');
        $periodTransferOut = (float) (clone $pq)->where('type', 'transfer')->sum('amount');
        $periodTransferIn  = (float) CashTransaction::where('transfer_to_account_id', $account->id)
            ->where('type', 'transfer')
            ->when($month, fn($q) => $q->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month))
            ->when(!$month, fn($q) => $q->whereYear('transaction_date', $year))
            ->sum('amount');

        $closingBalance = $openingBalance + $periodInflow - $periodOutflow - $periodTransferOut + $periodTransferIn;

        $transactions = (clone $pq)->with(['category', 'transferToAccount'])
            ->orderBy('transaction_date')->orderBy('created_at')->get();

        $categoryBreakdown = (clone $pq)->whereIn('type', ['inflow', 'outflow'])->with('category')->get()
            ->groupBy(fn($t) => $t->category?->name ?? 'Lainnya')
            ->map(fn($g) => [
                'name'  => $g->first()->category?->name ?? 'Lainnya',
                'type'  => $g->first()->type,
                'total' => $g->sum('amount'),
                'count' => $g->count(),
                'color' => $g->first()->category?->color ?? '#94a3b8',
            ])->values();

        return compact(
            'account', 'year', 'month',
            'openingBalance', 'periodInflow', 'periodOutflow', 'closingBalance',
            'transactions', 'categoryBreakdown'
        ) + ['periodLabel' => $this->periodLabel($year, $month)];
    }

    private function periodLabel(int $year, ?int $month): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $month ? (($months[$month] ?? '') . ' ' . $year) : "Tahun {$year}";
    }

    // ── PDF Export ────────────────────────────────────────────────────────────

    public function exportPdf(Request $request): Response
    {
        $data = $this->buildReportData($request);

        $pdf = app('dompdf.wrapper')
            ->loadView('reports.cashflow-pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'laporan-arus-kas-' . ($data['account']?->name ?? 'semua') . '-' . $data['periodLabel'] . '.pdf';

        return $pdf->download(str_replace(' ', '-', strtolower($filename)));
    }

    // ── Excel Export ──────────────────────────────────────────────────────────

    public function exportExcel(Request $request)
    {
        $data     = $this->buildReportData($request);
        $filename = 'laporan-arus-kas-' . ($data['account']?->name ?? 'semua') . '-' . $data['periodLabel'] . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CashFlowReportExport($data),
            str_replace(' ', '-', strtolower($filename))
        );
    }
}
