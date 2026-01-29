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
        
        // Build query - ambil langsung dari database transaksi
        $query = TransaksiHarian::with('warung')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        
        if ($warungId) {
            $query->where('warung_id', $warungId);
        }
        
        // Get all transactions and group by warung
        $transaksis = $query->get()->groupBy('warung_id');
        
        $data = $transaksis->map(function ($transactions, $warungId) use ($tanggalAwal, $tanggalAkhir) {
            $warung = $transactions->first()->warung;
            
            $operasional = PengeluaranOperasional::where('warung_id', $warungId)
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->sum('nominal');
            
            // Hitung transaksi buka dan tutup
            $hariBuka = $transactions->where('status', 'buka')->count();
            $hariTutup = $transactions->where('status', 'tutup')->count();
            
            $omset = $transactions->sum('omset');
            $profit = $omset - $operasional;
            
            // Flag tutup: semua transaksi dalam periode adalah tutup
            $isTutup = ($hariBuka == 0 && $hariTutup > 0);
            
            return [
                'warung' => $warung,
                'omset' => $omset,
                'operasional' => $operasional,
                'net_profit' => $profit,
                'dimsum' => $transactions->where('status', 'buka')->sum('dimsum_terjual'),
                'hari_kerja' => $hariBuka,
                'hari_tutup' => $hariTutup,
                'is_tutup' => $isTutup,
            ];
        })->sortBy(fn($item) => $item['warung']->nama_warung)->values();
        
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
        $tanggalAwal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        $warungId = $request->input('warung_id');
        
        // Build query - ambil langsung dari database transaksi
        $query = TransaksiHarian::with('warung')
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        
        if ($warungId) {
            $query->where('warung_id', $warungId);
        }
        
        // Get all transactions and group by warung
        $transaksis = $query->get()->groupBy('warung_id');
        
        $data = $transaksis->map(function ($transactions, $warungId) use ($tanggalAwal, $tanggalAkhir) {
            $warung = $transactions->first()->warung;
            
            $operasional = PengeluaranOperasional::where('warung_id', $warungId)
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->sum('nominal');
            
            $hariBuka = $transactions->where('status', 'buka')->count();
            $hariTutup = $transactions->where('status', 'tutup')->count();
            
            $omset = $transactions->sum('omset');
            $profit = $omset - $operasional;
            
            $isTutup = ($hariBuka == 0 && $hariTutup > 0);
            
            return [
                'warung' => $warung->nama_warung,
                'omset' => $omset,
                'operasional' => $operasional,
                'net_profit' => $profit,
                'dimsum' => $transactions->where('status', 'buka')->sum('dimsum_terjual'),
                'hari_kerja' => $hariBuka,
                'hari_tutup' => $hariTutup,
                'is_tutup' => $isTutup,
            ];
        })->sortBy('warung')->values();
        
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
