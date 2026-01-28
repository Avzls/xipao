<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StokGudang;
use App\Models\RestokGudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        $stoks = StokGudang::with('item')
            ->get()
            ->sortBy(function ($stok) {
                // Sort by status (low stock first) then by item name
                $priority = match($stok->status) {
                    'habis' => 0,
                    'menipis' => 1,
                    default => 2,
                };
                return $priority . $stok->item->nama_item;
            });
        
        return view('gudang.index', compact('stoks'));
    }

    public function restok()
    {
        $items = Item::orderBy('nama_item')->get();
        return view('gudang.restok', compact('items'));
    }

    public function storeRestok(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty_masuk' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        RestokGudang::create($validated);

        return redirect()->route('gudang.index')
            ->with('success', 'Restok berhasil ditambahkan!');
    }

    public function history(Request $request)
    {
        $query = RestokGudang::with('item')
            ->orderBy('tanggal_masuk', 'desc');
        
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        
        $restoks = $query->paginate(15);
        $items = Item::orderBy('nama_item')->get();
        
        return view('gudang.history', compact('restoks', 'items'));
    }
}
