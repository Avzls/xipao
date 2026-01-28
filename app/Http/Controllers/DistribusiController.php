<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\Item;
use App\Models\StokGudang;
use App\Models\DistribusiStok;
use Illuminate\Http\Request;

class DistribusiController extends Controller
{
    public function index(Request $request)
    {
        $query = DistribusiStok::with(['warung', 'item'])
            ->orderBy('tanggal_distribusi', 'desc');
        
        if ($request->filled('warung_id')) {
            $query->where('warung_id', $request->warung_id);
        }
        
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        
        $distribusis = $query->paginate(15);
        $warungs = Warung::aktif()->orderBy('nama_warung')->get();
        $items = Item::orderBy('nama_item')->get();
        
        return view('distribusi.index', compact('distribusis', 'warungs', 'items'));
    }

    public function create()
    {
        $warungs = Warung::aktif()->orderBy('nama_warung')->get();
        $items = Item::with('stokGudang')->orderBy('nama_item')->get();
        return view('distribusi.create', compact('warungs', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warung_id' => 'required|exists:warungs,id',
            'tanggal_distribusi' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        // Validate stock availability
        foreach ($validated['items'] as $itemData) {
            $stok = StokGudang::where('item_id', $itemData['item_id'])->first();
            if (!$stok || $stok->qty < $itemData['qty']) {
                $item = Item::find($itemData['item_id']);
                return back()->withErrors([
                    'items' => "Stok {$item->nama_item} tidak mencukupi. Tersedia: " . ($stok->qty ?? 0)
                ])->withInput();
            }
        }

        // Create distribution records
        foreach ($validated['items'] as $itemData) {
            DistribusiStok::create([
                'warung_id' => $validated['warung_id'],
                'item_id' => $itemData['item_id'],
                'qty_distribusi' => $itemData['qty'],
                'tanggal_distribusi' => $validated['tanggal_distribusi'],
                'keterangan' => $validated['keterangan'],
            ]);
        }

        return redirect()->route('distribusi.index')
            ->with('success', 'Distribusi stok berhasil disimpan!');
    }
}
