<?php

namespace App\Filament\Pages;

use App\Models\CashAccount;
use App\Models\CashTransaction;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Illuminate\Support\Collection;
use UnitEnum;

class CashFlowReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Laporan Arus Kas';

    protected static ?string $title = 'Laporan Arus Kas';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.cash-flow-report';

    // ── Filter state ──────────────────────────────────────────────────────────

    public ?string $account_id = null;
    public int $year;
    public ?int $month = null;

    public function mount(): void
    {
        $this->year  = (int) date('Y');
        $this->month = (int) date('m');

        // Default to the first active account
        $this->account_id = CashAccount::where('is_active', true)->first()?->id;
    }

    // ── Computed Report Data ──────────────────────────────────────────────────

    public function getReportData(): array
    {
        if (!$this->account_id) {
            return $this->emptyReport();
        }

        $account = CashAccount::find($this->account_id);
        if (!$account) {
            return $this->emptyReport();
        }

        // Period bounds
        $month = $this->month;
        $year  = $this->year;

        // Opening balance = opening_balance + all transactions BEFORE this period
        $beforeDate  = $month
            ? \Carbon\Carbon::create($year, $month, 1)->startOfMonth()
            : \Carbon\Carbon::create($year, 1, 1)->startOfYear();

        $beforeIn  = (float) $account->transactions()->where('type', 'inflow')
            ->where('transaction_date', '<', $beforeDate)->sum('amount');
        $transferIn = (float) CashTransaction::where('transfer_to_account_id', $account->id)
            ->where('type', 'transfer')
            ->where('transaction_date', '<', $beforeDate)->sum('amount');
        $beforeOut = (float) $account->transactions()->whereIn('type', ['outflow', 'transfer'])
            ->where('transaction_date', '<', $beforeDate)->sum('amount');

        $openingBalance = (float) $account->opening_balance + $beforeIn + $transferIn - $beforeOut;

        // Period query
        $periodQuery = $account->transactions()
            ->when($month, fn($q) => $q->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month))
            ->when(!$month, fn($q) => $q->whereYear('transaction_date', $year));

        $periodInflow  = (float) (clone $periodQuery)->where('type', 'inflow')->sum('amount');
        $periodOutflow = (float) (clone $periodQuery)->where('type', 'outflow')->sum('amount');
        $periodTransferOut = (float) (clone $periodQuery)->where('type', 'transfer')->sum('amount');
        $periodTransferIn  = (float) CashTransaction::where('transfer_to_account_id', $account->id)
            ->where('type', 'transfer')
            ->when($month, fn($q) => $q->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month))
            ->when(!$month, fn($q) => $q->whereYear('transaction_date', $year))
            ->sum('amount');

        $closingBalance = $openingBalance + $periodInflow - $periodOutflow - $periodTransferOut + $periodTransferIn;

        // All period transactions for the table
        $transactions = (clone $periodQuery)
            ->with(['category', 'transferToAccount'])
            ->orderBy('transaction_date')
            ->orderBy('created_at')
            ->get();

        // Category breakdown
        $categoryBreakdown = (clone $periodQuery)
            ->whereIn('type', ['inflow', 'outflow'])
            ->with('category')
            ->get()
            ->groupBy(fn($t) => $t->category?->name ?? 'Lainnya')
            ->map(fn($group) => [
                'name'  => $group->first()->category?->name ?? 'Lainnya',
                'type'  => $group->first()->type,
                'total' => $group->sum('amount'),
                'count' => $group->count(),
                'color' => $group->first()->category?->color ?? '#94a3b8',
            ])
            ->values();

        return compact(
            'account',
            'openingBalance',
            'periodInflow',
            'periodOutflow',
            'closingBalance',
            'transactions',
            'categoryBreakdown'
        );
    }

    private function emptyReport(): array
    {
        return [
            'account'           => null,
            'openingBalance'    => 0,
            'periodInflow'      => 0,
            'periodOutflow'     => 0,
            'closingBalance'    => 0,
            'transactions'      => collect(),
            'categoryBreakdown' => collect(),
        ];
    }

    // ── Helpers for Blade ─────────────────────────────────────────────────────

    public function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function getPeriodLabel(): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $this->month
            ? ($months[$this->month] ?? '') . ' ' . $this->year
            : 'Tahun ' . $this->year;
    }

    // ── Form ──────────────────────────────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                \Filament\Schemas\Components\Grid::make(3)->schema([
                    Select::make('account_id')
                        ->label('Rekening')
                        ->options(CashAccount::where('is_active', true)->pluck('name', 'id'))
                        ->live()
                        ->prefixIcon('heroicon-m-banknotes'),

                    Select::make('year')
                        ->label('Tahun')
                        ->options(collect(range(date('Y'), date('Y') - 5))->mapWithKeys(fn($y) => [$y => $y]))
                        ->live()
                        ->required(),

                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ])
                        ->live()
                        ->placeholder('Semua bulan (tahunan)'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    // ── Header Actions ────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(fn() => route('cashflow.report.pdf', [
                    'account_id' => $this->account_id,
                    'year'       => $this->year,
                    'month'      => $this->month,
                ]))
                ->openUrlInNewTab(),

            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(fn() => route('cashflow.report.excel', [
                    'account_id' => $this->account_id,
                    'year'       => $this->year,
                    'month'      => $this->month,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
