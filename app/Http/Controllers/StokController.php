<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StokGudang;
use App\Models\StokOpname;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index()
    {
        $items = Item::with(['stokGudang', 'latestOpname'])->orderBy('nama_item')->get();
        
        return view('stok.index', compact('items'));
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
}
