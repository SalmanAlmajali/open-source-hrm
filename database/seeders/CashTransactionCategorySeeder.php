<?php

namespace Database\Seeders;

use App\Models\CashTransactionCategory;
use Illuminate\Database\Seeder;

class CashTransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ── Inflow ────────────────────────────────────
            ['name' => 'Pendapatan Proyek',      'type' => 'inflow',  'color' => '#22c55e'],
            ['name' => 'Pendapatan Lainnya',     'type' => 'inflow',  'color' => '#84cc16'],
            ['name' => 'Penerimaan Piutang',     'type' => 'inflow',  'color' => '#10b981'],
            ['name' => 'Modal Awal / Investasi', 'type' => 'inflow',  'color' => '#6366f1'],

            // ── Outflow ───────────────────────────────────
            ['name' => 'Gaji Karyawan',          'type' => 'outflow', 'color' => '#ef4444'],
            ['name' => 'BPJS Ketenagakerjaan',   'type' => 'outflow', 'color' => '#f97316'],
            ['name' => 'BPJS Kesehatan',         'type' => 'outflow', 'color' => '#f97316'],
            ['name' => 'Pajak PPh 21',           'type' => 'outflow', 'color' => '#eab308'],
            ['name' => 'Pajak PPN',              'type' => 'outflow', 'color' => '#eab308'],
            ['name' => 'Sewa Kantor',            'type' => 'outflow', 'color' => '#8b5cf6'],
            ['name' => 'Utilitas (Listrik/Air)', 'type' => 'outflow', 'color' => '#06b6d4'],
            ['name' => 'Peralatan Kantor',       'type' => 'outflow', 'color' => '#64748b'],
            ['name' => 'Biaya ATK',              'type' => 'outflow', 'color' => '#64748b'],
            ['name' => 'Biaya Transportasi',     'type' => 'outflow', 'color' => '#0ea5e9'],
            ['name' => 'Biaya Komunikasi',       'type' => 'outflow', 'color' => '#0ea5e9'],
            ['name' => 'Operasional Lainnya',    'type' => 'outflow', 'color' => '#94a3b8'],
        ];

        foreach ($categories as $cat) {
            CashTransactionCategory::firstOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type']],
                ['color' => $cat['color']]
            );
        }
    }
}
