<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\Item;
use App\Models\StokGudang;
use App\Models\TransaksiHarian;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $warungs = Warung::aktif()->get();
        
        $query = TransaksiHarian::with('warung')
            ->orderBy('tanggal', 'desc');
        
        if ($request->filled('warung_id')) {
            $query->where('warung_id', $request->warung_id);
        }
        
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        
        $query->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);
        
        $transaksis = $query->paginate(50);
        
        // Prepare JSON data for Alpine.js table
        $transaksiJson = collect($transaksis->items())->map(function($tx) {
            return [
                'id' => $tx->id,
                'tanggal' => $tx->tanggal->format('Y-m-d'),
                'tanggal_formatted' => $tx->tanggal->translatedFormat('d M Y'),
                'warung' => $tx->warung->nama_warung,
                'status' => $tx->status ?? 'buka',
                'dimsum' => $tx->dimsum_terjual,
                'cash' => $tx->cash,
                'modal' => $tx->modal,
                'omset' => $tx->omset,
            ];
        })->values();
        
        $summary = [
            'total_omset' => TransaksiHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('omset'),
            'total_modal' => TransaksiHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('modal'),
            'total_dimsum' => TransaksiHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('dimsum_terjual'),
        ];
        
        // Get dimsum price for modal form
        $dimsum = Item::where('nama_item', 'like', '%dimsum%')->first();
        $hargaDimsum = $dimsum?->harga ?? 0;
        
        return view('transaksi.index', compact('warungs', 'transaksis', 'transaksiJson', 'summary', 'bulan', 'tahun', 'hargaDimsum'));
    }

    public function create()
    {
        $warungs = Warung::aktif()->orderBy('nama_warung')->get();
        
        // Get dimsum item for auto-calculate
        $dimsum = Item::where('nama_item', 'like', '%dimsum%')->first();
        $hargaDimsum = $dimsum?->harga ?? 0;
        
        return view('transaksi.create', compact('warungs', 'hargaDimsum'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warung_id' => 'required|exists:warungs,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:buka,tutup',
            'dimsum_terjual' => 'required|integer|min:0',
            'cash' => 'required|numeric|min:0',
            'modal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Check duplicate
        $exists = TransaksiHarian::where('warung_id', $validated['warung_id'])
            ->whereDate('tanggal', $validated['tanggal'])
            ->exists();
        
        if ($exists) {
            return back()->withErrors(['tanggal' => 'Transaksi untuk warung dan tanggal ini sudah ada.'])
                ->withInput();
        }

        // Create transaction (omset auto-calculated in model)
        $transaksi = TransaksiHarian::create([
            'warung_id' => $validated['warung_id'],
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
            'dimsum_terjual' => $validated['dimsum_terjual'],
            'cash' => $validated['cash'],
            'modal' => $validated['modal'],
            'keterangan' => $validated['keterangan'],
        ]);

        // Auto-reduce stock for dimsum
        $dimsum = Item::where('nama_item', 'like', '%dimsum%')->first();
        if ($dimsum) {
            $stok = StokGudang::where('item_id', $dimsum->id)->first();
            if ($stok) {
                $newQty = max(0, $stok->qty - $validated['dimsum_terjual']);
                $stok->update(['qty' => $newQty]);
            }
        }

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan! Stok dimsum dikurangi ' . $validated['dimsum_terjual'] . ' pcs.');
    }

    public function edit(TransaksiHarian $transaksi)
    {
        $warungs = Warung::aktif()->orderBy('nama_warung')->get();
        $dimsum = Item::where('nama_item', 'like', '%dimsum%')->first();
        $hargaDimsum = $dimsum?->harga ?? 0;
        
        return view('transaksi.edit', compact('transaksi', 'warungs', 'hargaDimsum'));
    }

    public function update(Request $request, TransaksiHarian $transaksi)
    {
        $validated = $request->validate([
            'dimsum_terjual' => 'required|integer|min:0',
            'cash' => 'required|numeric|min:0',
            'modal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Calculate stock difference for adjustment
        $oldDimsum = $transaksi->dimsum_terjual;
        $newDimsum = $validated['dimsum_terjual'];
        $diff = $newDimsum - $oldDimsum;

        $transaksi->update($validated);

        // Adjust stock if dimsum changed
        if ($diff != 0) {
            $dimsum = Item::where('nama_item', 'like', '%dimsum%')->first();
            if ($dimsum) {
                $stok = StokGudang::where('item_id', $dimsum->id)->first();
                if ($stok) {
                    $newQty = max(0, $stok->qty - $diff);
                    $stok->update(['qty' => $newQty]);
                }
            }
        }

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(TransaksiHarian $transaksi)
    {
        // Restore stock when deleting transaction
        $dimsum = Item::where('nama_item', 'like', '%dimsum%')->first();
        if ($dimsum) {
            $stok = StokGudang::where('item_id', $dimsum->id)->first();
            if ($stok) {
                $stok->update(['qty' => $stok->qty + $transaksi->dimsum_terjual]);
            }
        }
        
        $transaksi->delete();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus! Stok dikembalikan.');
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        
        $query = TransaksiHarian::with('warung')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('warung_id', 'asc');
        
        if ($request->filled('warung_id')) {
            $query->where('warung_id', $request->warung_id);
        }
        
        $transaksis = $query->get();
        
        // Calculate totals
        $totals = [
            'dimsum' => $transaksis->where('status', 'buka')->sum('dimsum_terjual'),
            'omset' => $transaksis->sum('omset'),
            'modal' => $transaksis->sum('modal'),
        ];
        
        $bulanNama = \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F');
        $periodeLabel = "$bulanNama $tahun";
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transaksi.pdf', compact('transaksis', 'totals', 'periodeLabel', 'bulan', 'tahun'));
        
        return $pdf->download("transaksi_detail_{$bulan}_{$tahun}.pdf");
    }
}
