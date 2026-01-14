<?php

namespace App\Filament\Resources\Employees\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public function redirectToEmployees()
    {
        return redirect()->to('employees?tableFilters[is_active][isActive]=false&tableFilters[is_inactive][isActive]=false');
    }
    public function redirectToInactiveEmployees()
    {
        return redirect()->to('employees?tableFilters[is_active][isActive]=false&tableFilters[is_inactive][isActive]=true');
    }
    public function redirectToActiveEmployees()
    {
        return redirect()->to('employees?tableFilters[is_active][isActive]=true&tableFilters[is_inactive][isActive]=false');
    }

    protected function getStats(): array
    {
        $commonAttributes = [
            'class' => 'cursor-pointer',
            'wire:click' => "redirectToEmployees()",
        ];

        return [
            //
            Stat::make('Jumlah Pegawai', Employee::count())
                ->label('Jumlah Pegawai')
                ->color('primary')
                ->description('Jumlah total pegawai dalam organisasi')
                ->extraAttributes($commonAttributes)
                ->icon('heroicon-o-user-group'),
            Stat::make('Pegawai Aktif', Employee::where('is_active', true)->count())
                ->color('success')
                ->label('Pegawai Aktif')
                ->extraAttributes(
                    [
                        'class' => 'cursor-pointer',
                        'wire:click' => "redirectToActiveEmployees()"
                    ]
                )
                ->description('Jumlah pegawai yang saat ini aktif')
                ->icon('heroicon-o-check-circle'),
            Stat::make('Pegawai Tidak Aktif', Employee::where('is_active', false)->count())
                ->label('Pegawai Tidak Aktif')
                ->description('Jumlah karyawan yang sudah tidak aktif lagi')
                ->color('danger')
                ->extraAttributes(
                    [
                        'class' => 'cursor-pointer',
                        'wire:click' => "redirectToInactiveEmployees()"
                    ]
                )
                ->icon('heroicon-o-x-circle'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->can('view_dashboard_stats');
    }
}
