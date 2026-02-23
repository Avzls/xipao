<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\Item;
use App\Models\TransaksiHarian;
use App\Models\TransaksiItem;
use App\Models\PengeluaranOperasional;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Build laporan data: per-product and per-warung breakdown
     */
    private function buildLaporanData($tanggalAwal, $tanggalAkhir, $warungId = null, $itemId = null)
    {
        $query = TransaksiHarian::with(['warung', 'transaksiItems.item'])
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        
        if ($warungId) {
            $query->where('warung_id', $warungId);
        }
        
        $transaksis = $query->get();
        
        // === Per-Product Breakdown ===
        $allItems = $transaksis->where('status', 'buka')->flatMap->transaksiItems;
        
        // Filter by product if specified
        if ($itemId) {
            $allItems = $allItems->where('item_id', $itemId);
        }
        
        $produkData = $allItems->groupBy('item_id')->map(function ($items) {
            $first = $items->first();
            return [
                'nama' => $first->item->nama_item ?? 'Unknown',
                'qty' => $items->sum('qty'),
                'omset' => $items->sum('subtotal'),
                'harga' => $first->harga,
            ];
        })->sortByDesc('omset')->values();
        
        $produkTotals = [
            'qty' => $produkData->sum('qty'),
            'omset' => $produkData->sum('omset'),
        ];

        // === Per-Warung Summary (for operasional & profit) ===
        $warungGroups = $transaksis->groupBy('warung_id');
        
        $warungData = $warungGroups->map(function ($transactions, $wId) use ($tanggalAwal, $tanggalAkhir, $itemId) {
            $warung = $transactions->first()->warung;
            
            $operasional = PengeluaranOperasional::where('warung_id', $wId)
                ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
                ->sum('nominal');
            
            $hariBuka = $transactions->where('status', 'buka')->count();
            $hariTutup = $transactions->where('status', 'tutup')->count();
            
            // Jika filter produk aktif, hitung omset dari subtotal produk tsb
            if ($itemId) {
                $filteredItems = $transactions->where('status', 'buka')->flatMap->transaksiItems->where('item_id', $itemId);
                $omset = $filteredItems->sum('subtotal');
            } else {
                $omset = $transactions->sum('omset');
            }
            
            $profit = $omset - $operasional;
            $isTutup = ($hariBuka == 0 && $hariTutup > 0);
            
            return [
                'warung' => $warung,
                'omset' => $omset,
                'operasional' => $operasional,
                'net_profit' => $profit,
                'hari_kerja' => $hariBuka,
                'hari_tutup' => $hariTutup,
                'is_tutup' => $isTutup,
            ];
        })->sortBy(fn($item) => $item['warung']->nama_warung)->values();
        
        $warungTotals = [
            'omset' => $warungData->sum('omset'),
            'operasional' => $warungData->sum('operasional'),
            'net_profit' => $warungData->sum('net_profit'),
        ];

        return compact('produkData', 'produkTotals', 'warungData', 'warungTotals');
    }

    public function konsolidasi(Request $request)
    {
        $allWarungs = Warung::aktif()->orderBy('nama_warung')->get();
        $allItems = Item::orderBy('nama_item')->get();
        
        $tanggalAwal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        $warungId = $request->input('warung_id');
        $itemId = $request->input('item_id');
        
        $data = $this->buildLaporanData($tanggalAwal, $tanggalAkhir, $warungId, $itemId);
        
        return view('laporan.konsolidasi', array_merge($data, compact('tanggalAwal', 'tanggalAkhir', 'allWarungs', 'allItems', 'warungId', 'itemId')));
    }

    public function exportExcel(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        $warungId = $request->input('warung_id');
        $itemId = $request->input('item_id');
        
        $filename = "laporan_{$tanggalAwal}_sd_{$tanggalAkhir}.xlsx";
        
        return Excel::download(new LaporanExport($tanggalAwal, $tanggalAkhir, $warungId, $itemId), $filename);
    }

    public function exportPdf(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->format('Y-m-d'));
        $warungId = $request->input('warung_id');
        $itemId = $request->input('item_id');
        
        $data = $this->buildLaporanData($tanggalAwal, $tanggalAkhir, $warungId, $itemId);
        
        $periodeLabel = Carbon::parse($tanggalAwal)->translatedFormat('d M Y') . ' - ' . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y');
        
        $pdf = Pdf::loadView('laporan.pdf', array_merge($data, compact('tanggalAwal', 'tanggalAkhir', 'periodeLabel')));
        
        return $pdf->download("laporan_{$tanggalAwal}_{$tanggalAkhir}.pdf");
    }
}
