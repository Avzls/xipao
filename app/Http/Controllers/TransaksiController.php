<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\Item;
use App\Models\StokGudang;
use App\Models\TransaksiHarian;
use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $warungs = Warung::aktif()->get();
        $items = Item::with('stokGudang')->orderBy('nama_item')->get();
        
        $query = TransaksiHarian::with(['warung', 'transaksiItems.item'])
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
                'items_count' => $tx->transaksiItems->count(),
                'items_summary' => $tx->transaksiItems->map(fn($ti) => $ti->item->nama_item . ' (' . $ti->qty . ')')->implode(', '),
                'cash' => $tx->cash,
                'modal' => $tx->modal,
                'omset' => $tx->omset,
            ];
        })->values();
        
        $summary = [
            'total_omset' => TransaksiHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('omset'),
            'total_modal' => TransaksiHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('modal'),
            'total_items' => TransaksiItem::whereHas('transaksiHarian', function($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            })->sum('qty'),
        ];
        
        // Items JSON for the form
        $itemsJson = $items->map(function($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama_item,
                'harga' => (float) $item->harga,
                'satuan' => $item->satuan,
                'stok' => $item->stokGudang?->qty ?? 0,
            ];
        })->values();
        
        return view('transaksi.index', compact('warungs', 'transaksis', 'transaksiJson', 'summary', 'bulan', 'tahun', 'items', 'itemsJson'));
    }

    public function create()
    {
        $warungs = Warung::aktif()->orderBy('nama_warung')->get();
        $items = Item::with('stokGudang')->orderBy('nama_item')->get();
        
        $itemsJson = $items->map(function($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama_item,
                'harga' => (float) $item->harga,
                'satuan' => $item->satuan,
                'stok' => $item->stokGudang?->qty ?? 0,
            ];
        })->values();
        
        return view('transaksi.create', compact('warungs', 'items', 'itemsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warung_id' => 'required|exists:warungs,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:buka,tutup',
            'cash' => 'required|numeric|min:0',
            'modal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // Check duplicate
        $exists = TransaksiHarian::where('warung_id', $validated['warung_id'])
            ->whereDate('tanggal', $validated['tanggal'])
            ->exists();
        
        if ($exists) {
            return back()->withErrors(['tanggal' => 'Transaksi untuk warung dan tanggal ini sudah ada.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated) {
            // Calculate dimsum_terjual for backward compat
            $dimsumTotal = 0;
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    $item = Item::find($itemData['item_id']);
                    if ($item && stripos($item->nama_item, 'dimsum') !== false) {
                        $dimsumTotal += $itemData['qty'];
                    }
                }
            }

            // Create transaction
            $transaksi = TransaksiHarian::create([
                'warung_id' => $validated['warung_id'],
                'tanggal' => $validated['tanggal'],
                'status' => $validated['status'],
                'dimsum_terjual' => $dimsumTotal,
                'cash' => $validated['cash'],
                'modal' => $validated['modal'],
                'keterangan' => $validated['keterangan'],
            ]);

            // Create items & reduce stock
            if (!empty($validated['items']) && $validated['status'] === 'buka') {
                foreach ($validated['items'] as $itemData) {
                    $item = Item::find($itemData['item_id']);
                    
                    TransaksiItem::create([
                        'transaksi_harian_id' => $transaksi->id,
                        'item_id' => $itemData['item_id'],
                        'qty' => $itemData['qty'],
                        'harga' => $item->harga,
                    ]);

                    // Reduce stock
                    $stok = StokGudang::where('item_id', $itemData['item_id'])->first();
                    if ($stok) {
                        $stok->decrement('qty', $itemData['qty']);
                    }
                }
            }
        });

        $itemCount = !empty($validated['items']) ? count($validated['items']) : 0;
        return redirect()->route('transaksi.index')
            ->with('success', "Transaksi berhasil disimpan! {$itemCount} produk dicatat, stok diperbarui.");
    }

    public function show(TransaksiHarian $transaksi)
    {
        $transaksi->load(['warung', 'transaksiItems.item']);
        return view('transaksi.show', compact('transaksi'));
    }

    public function edit(TransaksiHarian $transaksi)
    {
        $warungs = Warung::aktif()->orderBy('nama_warung')->get();
        $items = Item::with('stokGudang')->orderBy('nama_item')->get();
        $transaksi->load('transaksiItems.item');
        
        $itemsJson = $items->map(function($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama_item,
                'harga' => (float) $item->harga,
                'satuan' => $item->satuan,
                'stok' => $item->stokGudang?->qty ?? 0,
            ];
        })->values();

        $existingItemsJson = $transaksi->transaksiItems->map(function($ti) {
            return [
                'item_id' => $ti->item_id,
                'qty' => $ti->qty,
                'harga' => (float) $ti->harga,
                'nama' => $ti->item->nama_item,
                'satuan' => $ti->item->satuan,
            ];
        })->values();
        
        return view('transaksi.edit', compact('transaksi', 'warungs', 'items', 'itemsJson', 'existingItemsJson'));
    }

    public function update(Request $request, TransaksiHarian $transaksi)
    {
        $validated = $request->validate([
            'cash' => 'required|numeric|min:0',
            'modal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $transaksi) {
            // 1. Restore old stock
            foreach ($transaksi->transaksiItems as $oldItem) {
                $stok = StokGudang::where('item_id', $oldItem->item_id)->first();
                if ($stok) {
                    $stok->increment('qty', $oldItem->qty);
                }
            }

            // 2. Delete old items
            $transaksi->transaksiItems()->delete();

            // 3. Calculate dimsum_terjual for backward compat
            $dimsumTotal = 0;
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    $item = Item::find($itemData['item_id']);
                    if ($item && stripos($item->nama_item, 'dimsum') !== false) {
                        $dimsumTotal += $itemData['qty'];
                    }
                }
            }

            // 4. Update transaction
            $transaksi->update([
                'dimsum_terjual' => $dimsumTotal,
                'cash' => $validated['cash'],
                'modal' => $validated['modal'],
                'keterangan' => $validated['keterangan'],
            ]);

            // 5. Create new items & reduce stock
            if (!empty($validated['items']) && $transaksi->status === 'buka') {
                foreach ($validated['items'] as $itemData) {
                    $item = Item::find($itemData['item_id']);
                    
                    TransaksiItem::create([
                        'transaksi_harian_id' => $transaksi->id,
                        'item_id' => $itemData['item_id'],
                        'qty' => $itemData['qty'],
                        'harga' => $item->harga,
                    ]);

                    // Reduce stock
                    $stok = StokGudang::where('item_id', $itemData['item_id'])->first();
                    if ($stok) {
                        $stok->decrement('qty', $itemData['qty']);
                    }
                }
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui! Stok disesuaikan.');
    }

    public function destroy(TransaksiHarian $transaksi)
    {
        DB::transaction(function () use ($transaksi) {
            // Restore stock from all items
            foreach ($transaksi->transaksiItems as $ti) {
                $stok = StokGudang::where('item_id', $ti->item_id)->first();
                if ($stok) {
                    $stok->increment('qty', $ti->qty);
                }
            }
            
            $transaksi->delete(); // cascade deletes transaksi_items
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus! Stok dikembalikan.');
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        
        $query = TransaksiHarian::with(['warung', 'transaksiItems.item'])
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
            'items_qty' => $transaksis->flatMap->transaksiItems->sum('qty'),
            'omset' => $transaksis->sum('omset'),
            'modal' => $transaksis->sum('modal'),
        ];
        
        $bulanNama = \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F');
        $periodeLabel = "$bulanNama $tahun";
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transaksi.pdf', compact('transaksis', 'totals', 'periodeLabel', 'bulan', 'tahun'));
        
        return $pdf->download("transaksi_detail_{$bulan}_{$tahun}.pdf");
    }
}
