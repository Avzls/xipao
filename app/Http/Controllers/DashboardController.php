<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\StokGudang;
use App\Models\TransaksiHarian;
use App\Models\PengeluaranOperasional;
use App\Models\TransaksiItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all active warungs
        $warungs = Warung::aktif()->get();
        
        // Today's summary - Transaksi
        $hariIni = TransaksiHarian::with('transaksiItems')->hariIni()->get();
        $totalOmsetHariIni = $hariIni->sum('omset');
        $totalDimsumHariIni = TransaksiItem::whereHas('transaksiHarian', function($q) {
            $q->whereDate('tanggal', Carbon::today());
        })->sum('qty');
        
        // Today's summary - Operasional
        $operasionalHariIni = PengeluaranOperasional::whereDate('tanggal', Carbon::today())->sum('nominal');
        
        // This month summary
        $bulanIni = TransaksiHarian::bulanIni()->get();
        $totalOmsetBulanIni = $bulanIni->sum('omset');
        
        // This month - Operasional
        $operasionalBulanIni = PengeluaranOperasional::whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('nominal');
        
        // Last 7 days chart data - Improved accuracy
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            
            $totalOmset = TransaksiHarian::whereDate('tanggal', $date)->sum('omset');
            $totalOperasional = PengeluaranOperasional::whereDate('tanggal', $date)->sum('nominal');
            
            $last7Days->push([
                'tanggal' => $date->format('d M'),
                'omset' => (float) $totalOmset,
                'operasional' => (float) $totalOperasional,
                'net' => (float) ($totalOmset - $totalOperasional),
            ]);
        }
        
        // This month ranking
        $ranking = $warungs->map(function ($warung) {
            $omset = TransaksiHarian::where('warung_id', $warung->id)
                ->bulanIni()
                ->sum('omset');
            return [
                'warung' => $warung,
                'omset' => $omset,
            ];
        })->sortByDesc('omset')->values();
        
        $totalOmsetRanking = $ranking->sum('omset');
        
        return view('dashboard', compact(
            'warungs',
            'hariIni',
            'totalOmsetHariIni',
            'totalDimsumHariIni',
            'operasionalHariIni',
            'totalOmsetBulanIni',
            'operasionalBulanIni',
            'last7Days',
            'ranking',
            'totalOmsetRanking'
        ));
    }
}
