<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\Item;
use App\Models\StokGudang;
use App\Models\TransaksiHarian;
use App\Models\DistribusiStok;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all active warungs with today's omset
        $warungs = Warung::aktif()->get();
        
        // Today's summary
        $hariIni = TransaksiHarian::hariIni()->get();
        $totalOmsetHariIni = $hariIni->sum('omset');
        $totalDimsumHariIni = $hariIni->sum('dimsum_terjual');
        
        // This month summary
        $bulanIni = TransaksiHarian::bulanIni()->get();
        $totalOmsetBulanIni = $bulanIni->sum('omset');
        
        // Low stock alert
        $stokMenipis = StokGudang::with('item')
            ->whereColumn('qty', '<', 'min_stock')
            ->get();
        
        // Last 7 days chart data
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayData = [
                'tanggal' => $date->format('d M'),
                'total' => 0,
            ];
            
            foreach ($warungs as $warung) {
                $omset = TransaksiHarian::where('warung_id', $warung->id)
                    ->whereDate('tanggal', $date)
                    ->sum('omset');
                $dayData[$warung->nama_warung] = $omset;
                $dayData['total'] += $omset;
            }
            
            $last7Days->push($dayData);
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
            'totalOmsetBulanIni',
            'stokMenipis',
            'last7Days',
            'ranking',
            'totalOmsetRanking'
        ));
    }
}
