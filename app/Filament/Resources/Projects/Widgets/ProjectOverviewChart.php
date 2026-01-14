<?php

namespace App\Filament\Resources\Projects\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ProjectOverviewChart extends ChartWidget
{
    use InteractsWithPageFilters;
    
    protected ?string $heading = 'Proyek: Rencana dan Realisasi';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full'; // Lebar penuh

    protected bool $isCollapsible = true;

    protected function getData(): array
    {
        // 1. Ambil Nilai dari Filter Dashboard
        $year = $this->filters['year'] ?? date('Y');
        $month = $this->filters['month'] ?? null; // Bisa null
        $column = $this->filters['data_column'] ?? 'contract_value'; // Default Bruto

        // 2. Tentukan Rentang Waktu & Granularitas
        if ($month) {
            // JIKA BULAN DIPILIH -> Tampilkan Data HARIAN di bulan tersebut
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            
            // Query Trend Harian
            $trendQuery = fn ($query) => Trend::query($query)
                ->between(start: $start, end: $end)
                ->perDay();
                
            $dateFormat = 'd M'; // Label: 01 Jan
        } else {
            // JIKA BULAN KOSONG -> Tampilkan Data BULANAN dalam setahun
            $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end = Carbon::createFromDate($year, 12, 31)->endOfYear();
            
            // Query Trend Bulanan
            $trendQuery = fn ($query) => Trend::query($query)
                ->between(start: $start, end: $end)
                ->perMonth();
                
            $dateFormat = 'M'; // Label: Jan
        }

        // 3. Query Data (Realisasi vs Rencana) dengan kolom dinamis ($column)
        
        // Data Realisasi (Sudah SPK) -> Pakai Tanggal SPK
        $realisasi = $trendQuery(Project::whereNotNull('spk_number'))
            ->dateColumn('spk_date')
            ->sum($column); // Sum kolom yang dipilih user

        // Data Rencana (Belum SPK) -> Pakai Tanggal Rencana
        $rencana = $trendQuery(Project::whereNull('spk_number'))
            ->dateColumn('plan_date')
            ->sum($column);

        // 4. Return Data Chart
        return [
            'datasets' => [
                [
                    'label' => 'Realisasi (Deal)',
                    'data' => $realisasi->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#10b981', // Hijau
                    'borderColor' => '#10b981',
                    'barPercentage' => 0.6,
                ],
                [
                    'label' => 'Rencana (Prospek)',
                    'data' => $rencana->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#9ca3af', // Abu-abu
                    'borderColor' => '#9ca3af',
                    'barPercentage' => 0.6,
                ],
            ],
            'labels' => $realisasi->map(fn (TrendValue $value) => Carbon::parse($value->date)->format($dateFormat)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    // Update Judul dinamis agar user tahu sedang lihat data apa
    public function getDescription(): ?string
    {
        $columnLabel = ($this->filters['data_column'] ?? 'contract_value') === 'net_income' ? 'Net Income' : 'Nilai Kontrak';
        $periodLabel = $this->filters['year'] ?? date('Y');
        
        if (!empty($this->filters['month'])) {
            $periodLabel = Carbon::createFromDate(null, $this->filters['month'])->translatedFormat('F') . " " . $periodLabel;
        }

        return "Menampilkan data {$columnLabel} periode {$periodLabel}.";
    }
  
    public static function canView(): bool
    {
        return auth()->user()->can('view_project_overview');
    }
}
