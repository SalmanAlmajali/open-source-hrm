<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CashAccounts\CashAccountResource;
use App\Models\CashAccount;
use App\Models\CashReceivable;
use App\Models\CashTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashBalanceSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $accounts  = CashAccount::where('is_active', true)->get();
        $stats     = [];

        // ── Per-account balance cards ──────────────────────────────────────────
        foreach ($accounts as $account) {
            $balance = $account->currentBalance();

            $stats[] = Stat::make($account->name, 'Rp ' . number_format($balance, 0, ',', '.'))
                ->description(match ($account->type) {
                    'cash'     => '💵 Kas Tunai',
                    'bank'     => '🏦 Rekening Bank',
                    'e-wallet' => '📱 Dompet Digital',
                    default    => '-',
                })
                ->color($balance >= 0 ? 'success' : 'danger')
                ->icon($balance >= 0 ? 'heroicon-o-banknotes' : 'heroicon-o-exclamation-triangle')
                ->extraAttributes([
                    'class'      => 'cursor-pointer',
                    'wire:click' => "redirectToAccount()",
                ]);
        }

        // ── Total across all cash accounts ─────────────────────────────────────
        $totalBalance = $accounts->sum(fn($a) => $a->currentBalance());

        $stats[] = Stat::make('Total Saldo Kas', 'Rp ' . number_format($totalBalance, 0, ',', '.'))
            ->description('Gabungan semua rekening aktif')
            ->color($totalBalance >= 0 ? 'primary' : 'danger')
            ->icon('heroicon-o-chart-bar');

        // ── Pending receivables summary ────────────────────────────────────────
        $pendingCount  = CashReceivable::where('status', 'pending')->count();
        $pendingAmount = CashReceivable::where('status', 'pending')->sum('receivable_amount');

        $stats[] = Stat::make(
            'Piutang Belum Diterima',
            'Rp ' . number_format((float) $pendingAmount, 0, ',', '.')
        )
            ->description("{$pendingCount} piutang proyek menunggu pembayaran")
            ->color('warning')
            ->icon('heroicon-o-document-currency-dollar');

        // ── This month's inflow / outflow ──────────────────────────────────────
        $now         = now();
        $monthInflow = CashTransaction::where('type', 'inflow')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->sum('amount');

        $monthOutflow = CashTransaction::where('type', 'outflow')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->sum('amount');

        $stats[] = Stat::make(
            'Pemasukan ' . $now->translatedFormat('F Y'),
            'Rp ' . number_format((float) $monthInflow, 0, ',', '.')
        )
            ->description('Total uang masuk bulan ini')
            ->color('success')
            ->icon('heroicon-o-arrow-trending-up');

        $stats[] = Stat::make(
            'Pengeluaran ' . $now->translatedFormat('F Y'),
            'Rp ' . number_format((float) $monthOutflow, 0, ',', '.')
        )
            ->description('Total uang keluar bulan ini')
            ->color('danger')
            ->icon('heroicon-o-arrow-trending-down');

        return $stats;
    }

    public function redirectToAccount(): void
    {
        $this->redirect(CashAccountResource::getUrl('index'));
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->can('view_dashboard_stats') ?? true;
    }
}
