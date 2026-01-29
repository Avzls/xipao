<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\TransaksiHarian;
use App\Models\PengeluaranOperasional;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function konsolidasi(Request $request)
    {
        $allWarungs = Warung::aktif()->orderBy('nama_warung')->get();
        
        // Date range filter
        $tanggalAwal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        
        // Warung filter
        $warungId = $request->input('warung_id');
        if ($warungId) {
            $warungs = $allWarungs->where('id', $warungId);
        } else {
            $warungs = $allWarungs;
        }
        
        $data = $warungs->map(function ($warung) use ($tanggalAwal, $tanggalAkhir) {
            $transaksi = TransaksiHarian::where('warung_id', $warung->id)
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            
            $operasional = PengeluaranOperasional::where('warung_id', $warung->id)
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->sum('nominal');
            
            $omset = $transaksi->sum('omset');
            $profit = $omset - $operasional;
            
            return [
                'warung' => $warung,
                'omset' => $omset,
                'operasional' => $operasional,
                'net_profit' => $profit,
                'dimsum' => $transaksi->sum('dimsum_terjual'),
                'hari_kerja' => $transaksi->count(),
            ];
        });
        
        $totals = [
            'omset' => $data->sum('omset'),
            'operasional' => $data->sum('operasional'),
            'net_profit' => $data->sum('net_profit'),
            'dimsum' => $data->sum('dimsum'),
        ];
        
        return view('laporan.konsolidasi', compact('data', 'totals', 'tanggalAwal', 'tanggalAkhir', 'allWarungs', 'warungId'));
    }

    public function exportExcel(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        $warungId = $request->input('warung_id');
        
        $filename = "laporan_{$tanggalAwal}_sd_{$tanggalAkhir}.xlsx";
        
        return Excel::download(new LaporanExport($tanggalAwal, $tanggalAkhir, $warungId), $filename);
    }

    public function exportPdf(Request $request)
    {
        $allWarungs = Warung::aktif()->get();
        
        $tanggalAwal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        $warungId = $request->input('warung_id');
        
        // Filter by warung if specified
        if ($warungId) {
            $warungs = $allWarungs->where('id', $warungId);
        } else {
            $warungs = $allWarungs;
        }
        
        $data = $warungs->map(function ($warung) use ($tanggalAwal, $tanggalAkhir) {
            $transaksi = TransaksiHarian::where('warung_id', $warung->id)
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            
            $operasional = PengeluaranOperasional::where('warung_id', $warung->id)
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->sum('nominal');
            
            $omset = $transaksi->sum('omset');
            $profit = $omset - $operasional;
            
            return [
                'warung' => $warung->nama_warung,
                'omset' => $omset,
                'operasional' => $operasional,
                'net_profit' => $profit,
                'dimsum' => $transaksi->sum('dimsum_terjual'),
            ];
        });
        
        $totals = [
            'omset' => $data->sum('omset'),
            'operasional' => $data->sum('operasional'),
            'net_profit' => $data->sum('net_profit'),
            'dimsum' => $data->sum('dimsum'),
        ];
        
        $periodeLabel = Carbon::parse($tanggalAwal)->translatedFormat('d M Y') . ' - ' . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y');
        
        $pdf = Pdf::loadView('laporan.pdf', compact('data', 'totals', 'tanggalAwal', 'tanggalAkhir', 'periodeLabel'));
        
        return $pdf->download("laporan_{$tanggalAwal}_{$tanggalAkhir}.pdf");
    }
}
