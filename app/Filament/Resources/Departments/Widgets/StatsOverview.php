<?php

namespace App\Filament\Resources\Departments\Widgets;

use App\Filament\Resources\Admins\AdminResource;
use App\Filament\Resources\Departments\DepartmentResource;
use App\Models\Employee;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Department;

class StatsOverview extends BaseWidget
{
    public function redirectToDepartments()
    {
        return redirect()->to(DepartmentResource::getUrl());
    }
    public function redirectToAdmins()
    {
        return redirect()->to(AdminResource::getUrl());
    }

    protected function getStats(): array
    {
        return [
            //
            Stat::make('Jumlah Departemen', Department::count())
                ->label('Jumlah Departemen')
                ->color('primary')
                ->description('Jumlah total departemen dalam organisasi')
                ->icon('heroicon-o-rectangle-group')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "redirectToDepartments()",
                ])
            // ->url(route('filament.admin.resources.departments.index')),
            ,
            Stat::make('Admin HR', Employee::role('super_admin')->count())
                ->label('Jumlah Admin HR')
                ->color('success')
                ->description('Jumlah total admin HR dalam organisasi')
                ->icon('heroicon-o-user-group')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "redirectToAdmins()",
                ])




        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->can('view_dashboard_stats');
    }
}
