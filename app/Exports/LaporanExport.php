<?php

namespace App\Exports;

use App\Models\Warung;
use App\Models\TransaksiHarian;
use App\Models\TransaksiItem;
use App\Models\PengeluaranOperasional;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;

class LaporanExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $warungId;
    protected $itemId;

    public function __construct($tanggalAwal, $tanggalAkhir, $warungId = null, $itemId = null)
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->warungId = $warungId;
        $this->itemId = $itemId;
    }

    public function collection()
    {
        $query = TransaksiHarian::with(['warung', 'transaksiItems.item'])
            ->whereBetween('tanggal', [$this->tanggalAwal, $this->tanggalAkhir]);
        
        if ($this->warungId) {
            $query->where('warung_id', $this->warungId);
        }
        
        $transaksis = $query->get();
        $allItems = $transaksis->where('status', 'buka')->flatMap->transaksiItems;
        
        // Filter by product if specified
        if ($this->itemId) {
            $allItems = $allItems->where('item_id', $this->itemId);
        }
        
        // Per-product rows
        $data = $allItems->groupBy('item_id')->map(function ($items) {
            $first = $items->first();
            return [
                'produk' => $first->item->nama_item ?? 'Unknown',
                'harga' => $first->harga,
                'qty' => $items->sum('qty'),
                'omset' => $items->sum('subtotal'),
            ];
        })->sortByDesc('omset')->values();

        // Totals row
        $data->push([
            'produk' => 'TOTAL',
            'harga' => '',
            'qty' => $data->sum('qty'),
            'omset' => $data->sum('omset'),
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Produk',
            'Harga Satuan',
            'Qty Terjual',
            'Omset (Rp)',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#,##0',
            'D' => '#,##0',
        ];
    }

    public function title(): string
    {
        return "Laporan";
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8B0000'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F0F0F0'],
                ],
            ],
        ];
    }
}
