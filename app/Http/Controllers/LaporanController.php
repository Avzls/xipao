<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\TransaksiHarian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    public function konsolidasi(Request $request)
    {
        $warungs = Warung::aktif()->get();
        
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        
        $data = $warungs->map(function ($warung) use ($bulan, $tahun) {
            $transaksi = TransaksiHarian::where('warung_id', $warung->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
            
            return [
                'warung' => $warung,
                'omset' => $transaksi->sum('omset'),
                'modal' => $transaksi->sum('modal'),
                'profit' => $transaksi->sum('omset') - $transaksi->sum('modal'),
                'dimsum' => $transaksi->sum('dimsum_terjual'),
                'hari_kerja' => $transaksi->count(),
            ];
        });
        
        $totals = [
            'omset' => $data->sum('omset'),
            'modal' => $data->sum('modal'),
            'profit' => $data->sum('profit'),
            'dimsum' => $data->sum('dimsum'),
        ];
        
        return view('laporan.konsolidasi', compact('data', 'totals', 'bulan', 'tahun'));
    }

    public function exportExcel(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        
        $filename = "laporan_konsolidasi_{$bulan}_{$tahun}.xlsx";
        
        return Excel::download(new LaporanExport($bulan, $tahun), $filename);
    }

    public function exportPdf(Request $request)
    {
        $warungs = Warung::aktif()->get();
        
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        
        $data = $warungs->map(function ($warung) use ($bulan, $tahun) {
            $transaksi = TransaksiHarian::where('warung_id', $warung->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
            
            return [
                'warung' => $warung->nama_warung,
                'omset' => $transaksi->sum('omset'),
                'modal' => $transaksi->sum('modal'),
                'profit' => $transaksi->sum('omset') - $transaksi->sum('modal'),
                'dimsum' => $transaksi->sum('dimsum_terjual'),
            ];
        });
        
        $totals = [
            'omset' => $data->sum('omset'),
            'modal' => $data->sum('modal'),
            'profit' => $data->sum('profit'),
            'dimsum' => $data->sum('dimsum'),
        ];
        
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
        
        $pdf = Pdf::loadView('laporan.pdf', compact('data', 'totals', 'bulan', 'tahun', 'namaBulan'));
        
        return $pdf->download("laporan_konsolidasi_{$bulan}_{$tahun}.pdf");
    }
}
