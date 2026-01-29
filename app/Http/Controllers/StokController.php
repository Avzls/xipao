<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StokGudang;
use App\Models\StokOpname;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::with(['stokGudang', 'latestOpname'])->orderBy('nama_item')->get();
        
        // History dari restok dengan filter tanggal
        $query = \App\Models\RestokGudang::with('item')->orderBy('tanggal_masuk', 'desc');
        
        if ($request->filled('from')) {
            $query->whereDate('tanggal_masuk', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_masuk', '<=', $request->to);
        }
        
        $histories = $query->take(50)->get();
        
        return view('stok.index', compact('items', 'histories'));
    }

    public function create()
    {
        return view('stok.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $item = Item::create([
            'nama_item' => $validated['nama_item'],
            'satuan' => $validated['satuan'],
            'kategori' => 'produk',
            'harga' => $validated['harga'],
        ]);

        // Create initial stock
        StokGudang::create([
            'item_id' => $item->id,
            'qty' => $validated['stok'],
        ]);

        return redirect()->route('stok.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $stok)
    {
        $stok->load('stokGudang');
        return view('stok.edit', compact('stok'));
    }

    public function update(Request $request, Item $stok)
    {
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $stok->update([
            'nama_item' => $validated['nama_item'],
            'satuan' => $validated['satuan'],
            'harga' => $validated['harga'],
        ]);

        // Update stock
        $stok->stokGudang()->updateOrCreate(
            ['item_id' => $stok->id],
            ['qty' => $validated['stok']]
        );

        return redirect()->route('stok.index')
            ->with('success', 'Barang berhasil diupdate.');
    }

    public function destroy(Item $stok)
    {
        $stok->delete();

        return redirect()->route('stok.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    // Stock Opname
    public function opname()
    {
        $items = Item::with('stokGudang')->orderBy('nama_item')->get();
        return view('stok.opname', compact('items'));
    }

    public function storeOpname(Request $request)
    {
        $validated = $request->validate([
            'tanggal_opname' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty_fisik' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $data) {
            $item = Item::with('stokGudang')->find($data['item_id']);
            $qtySistem = $item->stokGudang?->qty ?? 0;
            
            $opname = StokOpname::create([
                'item_id' => $data['item_id'],
                'tanggal_opname' => $validated['tanggal_opname'],
                'qty_sistem' => $qtySistem,
                'qty_fisik' => $data['qty_fisik'],
            ]);

            // Auto-adjust stock
            if ($opname->selisih != 0) {
                $item->stokGudang()->updateOrCreate(
                    ['item_id' => $item->id],
                    ['qty' => $data['qty_fisik']]
                );
                $opname->update(['is_adjusted' => true]);
            }
        }

        return redirect()->route('stok.index')
            ->with('success', 'Stock opname berhasil! Stok sudah disesuaikan.');
    }

    // History Restok
    public function history(Request $request)
    {
        $query = \App\Models\RestokGudang::with('item')->orderBy('tanggal_masuk', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('nama_item', 'like', "%{$search}%");
            })->orWhere('supplier', 'like', "%{$search}%");
        }

        // Filter by date
        if ($request->filled('from')) {
            $query->whereDate('tanggal_masuk', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_masuk', '<=', $request->to);
        }

        $histories = $query->paginate(15)->withQueryString();

        return view('stok.history', compact('histories'));
    }

    // Restok - Tambah Stock ke Item Existing
    public function restok(Item $stok)
    {
        return view('stok.restok', ['item' => $stok]);
    }

    public function storeRestok(Request $request, Item $stok)
    {
        $validated = $request->validate([
            'qty_masuk' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
        ]);

        // Create restok record
        \App\Models\RestokGudang::create([
            'item_id' => $stok->id,
            'qty_masuk' => $validated['qty_masuk'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
        ]);

        // Update stock
        $stok->stokGudang()->updateOrCreate(
            ['item_id' => $stok->id],
            ['qty' => ($stok->stokGudang->qty ?? 0) + $validated['qty_masuk']]
        );

        return redirect()->route('stok.index')
            ->with('success', 'Stock berhasil ditambahkan: +' . $validated['qty_masuk'] . ' ' . $stok->nama_item);
    }

    public function exportHistoryPdf(Request $request)
    {
        $query = \App\Models\RestokGudang::with('item')->orderBy('tanggal_masuk', 'desc');
        
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        
        $query->whereDate('tanggal_masuk', '>=', $from);
        $query->whereDate('tanggal_masuk', '<=', $to);
        
        $histories = $query->get();
        $total = $histories->sum('qty_masuk');
        
        $periodeLabel = \Carbon\Carbon::parse($from)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($to)->translatedFormat('d M Y');
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('stok.history-pdf', compact('histories', 'total', 'periodeLabel'));
        
        return $pdf->download('history-stock-masuk-' . $from . '-' . $to . '.pdf');
    }
}
