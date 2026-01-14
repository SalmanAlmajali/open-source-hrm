<?php

namespace App\Filament\Resources\Shield\Permissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('readable_name')
                    ->label('Keterangan Izin')
                    ->state(fn ($record) => self::getLabel($record->name))
                    ->searchable(['name'])
                    ->sortable(['name'])
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-key')
                    ->description(fn ($record) => $record->name),

                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('roles_count')
                    ->label('Digunakan di')
                    ->counts('roles')
                    ->suffix(' Peran')
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getLabel(string $permissionName): string
    {
        $actions = [
            'view_any' => 'Melihat Daftar',
            'view' => 'Melihat Detail',
            'create' => 'Membuat',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            'delete_any' => 'Hapus Banyak',
            'restore' => 'Mengembalikan',
            'force_delete' => 'Hapus Permanen',
        ];

        $resources = [
            'admins' => 'Admin',
            'departments' => 'Departemen',
            'employees' => 'Pegawai',
            'positions' => 'Jabatan',
            'projects' => 'Proyek',
            'roles' => 'Peran (Role)',
            'permissions' => 'Izin Akses',
            'dashboard_stats' => 'Statistik Dashboard',
            'project_overview' => 'Overview Proyek',
        ];

        // Format standar: action_resource (contoh: create_projects)

        $parts = explode('_', $permissionName);

        // Cek bagian akhir string untuk mencari nama Resource
        $resourceKey = end($parts);

        // Sisanya adalah Action
        array_pop($parts); // Hapus elemen terakhir (resource)
        $actionKey = implode('_', $parts); // Gabungkan sisa menjadi action

        $actionLabel = $actions[$actionKey] ?? ucfirst(str_replace('_', ' ', $actionKey));
        $resourceLabel = $resources[$resourceKey] ?? ucfirst($resourceKey);

        return "{$actionLabel} {$resourceLabel}";
    }
}
