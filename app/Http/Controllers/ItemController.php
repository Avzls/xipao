<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StokGudang;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('stokGudang')
            ->orderBy('kategori')
            ->orderBy('nama_item')
            ->get();
        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'kategori' => 'required|in:produk,operasional,kemasan',
            'harga_modal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
        ]);

        $item = Item::create([
            'nama_item' => $validated['nama_item'],
            'satuan' => $validated['satuan'],
            'kategori' => $validated['kategori'],
            'harga_modal' => $validated['harga_modal'],
            'harga_jual' => $validated['harga_jual'],
        ]);

        // Create stock record
        StokGudang::create([
            'item_id' => $item->id,
            'qty' => 0,
            'min_stock' => $validated['min_stock'] ?? 0,
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit(Item $item)
    {
        $item->load('stokGudang');
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'kategori' => 'required|in:produk,operasional,kemasan',
            'harga_modal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
        ]);

        $item->update([
            'nama_item' => $validated['nama_item'],
            'satuan' => $validated['satuan'],
            'kategori' => $validated['kategori'],
            'harga_modal' => $validated['harga_modal'],
            'harga_jual' => $validated['harga_jual'],
        ]);

        // Update min stock
        if ($item->stokGudang) {
            $item->stokGudang->update(['min_stock' => $validated['min_stock'] ?? 0]);
        }

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus!');
    }
}
