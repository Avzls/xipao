<?php

namespace App\Exports;

use App\Models\Warung;
use App\Models\TransaksiHarian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $warungs = Warung::aktif()->get();
        
        $data = $warungs->map(function ($warung) {
            $transaksi = TransaksiHarian::where('warung_id', $warung->id)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun);
            
            $omset = $transaksi->sum('omset');
            $modal = $transaksi->sum('modal');
            
            return [
                'warung' => $warung->nama_warung,
                'dimsum' => number_format($transaksi->sum('dimsum_terjual'), 0, ',', '.'),
                'omset' => number_format($omset, 0, ',', '.'),
                'modal' => number_format($modal, 0, ',', '.'),
                'profit' => number_format($omset - $modal, 0, ',', '.'),
                'hari_kerja' => $transaksi->count(),
            ];
        });

        // Add totals row
        $totalOmset = $data->sum(fn($d) => (int) str_replace('.', '', $d['omset']));
        $totalModal = $data->sum(fn($d) => (int) str_replace('.', '', $d['modal']));
        $totalProfit = $data->sum(fn($d) => (int) str_replace('.', '', $d['profit']));
        $totalDimsum = $data->sum(fn($d) => (int) str_replace('.', '', $d['dimsum']));
        
        $data->push([
            'warung' => 'TOTAL',
            'dimsum' => number_format($totalDimsum, 0, ',', '.'),
            'omset' => number_format($totalOmset, 0, ',', '.'),
            'modal' => number_format($totalModal, 0, ',', '.'),
            'profit' => number_format($totalProfit, 0, ',', '.'),
            'hari_kerja' => '',
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'Warung',
            'Dimsum Terjual',
            'Omset (Rp)',
            'Modal (Rp)',
            'Profit (Rp)',
            'Hari Kerja',
        ];
    }

    public function title(): string
    {
        $namaBulan = \Carbon\Carbon::create()->month($this->bulan)->translatedFormat('F');
        return "Laporan {$namaBulan} {$this->tahun}";
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        return [
            // Header style
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8B0000'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Total row style
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
