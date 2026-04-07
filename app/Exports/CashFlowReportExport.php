<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class CashFlowReportExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    public function __construct(private array $data) {}

    public function collection(): Collection
    {
        $rows = collect();

        // Summary rows
        $rows->push(['LAPORAN ARUS KAS', '', '', '', '']);
        $rows->push(['Rekening', $this->data['account']?->name ?? '-', '', '', '']);
        $rows->push(['Periode', $this->data['periodLabel'], '', '', '']);
        $rows->push(['', '', '', '', '']);

        $rows->push(['Saldo Awal',     '', '', '', $this->data['openingBalance']]);
        $rows->push(['Total Pemasukan','', '', '', $this->data['periodInflow']]);
        $rows->push(['Total Pengeluaran','','','',  $this->data['periodOutflow']]);
        $rows->push(['Saldo Akhir',    '', '', '', $this->data['closingBalance']]);
        $rows->push(['', '', '', '', '']);
        $rows->push(['--- DETAIL TRANSAKSI ---', '', '', '', '']);

        // Transaction rows
        foreach ($this->data['transactions'] as $trx) {
            $rows->push([
                $trx->transaction_date->format('d/m/Y'),
                $trx->reference_number ?? '-',
                $trx->description ?? '-',
                $trx->category?->name ?? ($trx->type === 'transfer' ? 'Transfer' : '-'),
                $trx->type === 'inflow' ? $trx->amount : -$trx->amount,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Tanggal', 'No. Referensi', 'Keterangan', 'Kategori', 'Jumlah (Rp)'];
    }

    public function title(): string
    {
        return 'Laporan Arus Kas';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1  => ['font' => ['bold' => true, 'size' => 14]],  // Title row
            10 => ['font' => ['bold' => true]],                  // Column headers (row after summaries)
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 40,
            'D' => 25,
            'E' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Format the Jumlah column as currency
                $event->sheet->getStyle('E')->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0;[Red]"Rp "-#,##0');
            },
        ];
    }
}
