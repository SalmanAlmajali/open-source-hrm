<?php

namespace App\Filament\Widgets;

use App\Models\CashTransaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class CashFlowChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Arus Kas: Pemasukan vs Pengeluaran';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected bool $isCollapsible = true;

    protected function getData(): array
    {
        $year  = (int) ($this->filters['year'] ?? date('Y'));
        $month = $this->filters['month'] ?? null;

        if ($month) {
            $start      = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end        = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $trendFn    = fn($q) => Trend::query($q)->between($start, $end)->perDay();
            $dateFormat = 'd M';
        } else {
            $start      = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end        = Carbon::createFromDate($year, 12, 31)->endOfYear();
            $trendFn    = fn($q) => Trend::query($q)->between($start, $end)->perMonth();
            $dateFormat = 'M';
        }

        $inflows  = $trendFn(CashTransaction::where('type', 'inflow'))
            ->dateColumn('transaction_date')
            ->sum('amount');

        $outflows = $trendFn(CashTransaction::where('type', 'outflow'))
            ->dateColumn('transaction_date')
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label'           => '⬆ Pemasukan',
                    'data'            => $inflows->map(fn(TrendValue $v) => $v->aggregate),
                    'backgroundColor' => '#10b981',
                    'borderColor'     => '#10b981',
                    'borderRadius'    => 6,
                    'barPercentage'   => 0.6,
                ],
                [
                    'label'           => '⬇ Pengeluaran',
                    'data'            => $outflows->map(fn(TrendValue $v) => $v->aggregate),
                    'backgroundColor' => '#ef4444',
                    'borderColor'     => '#ef4444',
                    'borderRadius'    => 6,
                    'barPercentage'   => 0.6,
                ],
            ],
            'labels' => $inflows->map(fn(TrendValue $v) => Carbon::parse($v->date)->translatedFormat($dateFormat)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        $year  = $this->filters['year'] ?? date('Y');
        $month = $this->filters['month'] ?? null;

        $period = $month
            ? Carbon::createFromDate(null, $month)->translatedFormat('F') . ' ' . $year
            : "Tahun {$year}";

        return "Grafik arus kas periode {$period}.";
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->can('view_dashboard_stats') ?? true;
    }
}
