<?php

namespace App\Exports;

use App\Models\Warung;
use App\Models\TransaksiHarian;
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

    public function __construct($tanggalAwal, $tanggalAkhir, $warungId = null)
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->warungId = $warungId;
    }

    public function collection()
    {
        $allWarungs = Warung::aktif()->get();
        
        // Filter by warung if specified
        if ($this->warungId) {
            $warungs = $allWarungs->where('id', $this->warungId);
        } else {
            $warungs = $allWarungs;
        }
        
        $data = $warungs->map(function ($warung) {
            $transaksi = TransaksiHarian::where('warung_id', $warung->id)
                ->whereBetween('tanggal', [$this->tanggalAwal, $this->tanggalAkhir]);
            
            $operasional = PengeluaranOperasional::where('warung_id', $warung->id)
                ->whereBetween('tanggal', [$this->tanggalAwal, $this->tanggalAkhir])
                ->sum('nominal');
            
            $omset = $transaksi->sum('omset');
            $profit = $omset - $operasional;
            
            return [
                'warung' => $warung->nama_warung,
                'dimsum' => $transaksi->sum('dimsum_terjual'),
                'omset' => $omset,
                'operasional' => $operasional,
                'profit' => $profit,
                'hari_kerja' => $transaksi->count(),
            ];
        });

        // Add totals row
        $totalOmset = $data->sum('omset');
        $totalOperasional = $data->sum('operasional');
        $totalProfit = $data->sum('profit');
        $totalDimsum = $data->sum('dimsum');
        
        $data->push([
            'warung' => 'TOTAL',
            'dimsum' => $totalDimsum,
            'omset' => $totalOmset,
            'operasional' => $totalOperasional,
            'profit' => $totalProfit,
            'hari_kerja' => '',
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Warung',
            'Dimsum',
            'Omset (Rp)',
            'Operasional (Rp)',
            'Profit (Rp)',
            'Hari Kerja',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '#,##0',
            'D' => '#,##0',
            'E' => '#,##0',
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
