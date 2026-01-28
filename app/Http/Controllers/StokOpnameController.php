<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StokOpname;
use App\Models\StokGudang;
use Illuminate\Http\Request;

class StokOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StokOpname::with('item')
            ->orderBy('tanggal_opname', 'desc');
        
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $opnames = $query->paginate(20);
        $items = Item::orderBy('nama_item')->get();
        
        return view('stok-opname.index', compact('opnames', 'items'));
    }

    public function create()
    {
        $items = Item::with('stokGudang')->orderBy('nama_item')->get();
        
        return view('stok-opname.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_opname' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty_fisik' => 'required|integer|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);

        foreach ($validated['items'] as $data) {
            $item = Item::with('stokGudang')->find($data['item_id']);
            $qtySistem = $item->stokGudang?->qty ?? 0;
            
            StokOpname::create([
                'item_id' => $data['item_id'],
                'tanggal_opname' => $validated['tanggal_opname'],
                'qty_sistem' => $qtySistem,
                'qty_fisik' => $data['qty_fisik'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('stok-opname.index')
            ->with('success', 'Stock opname berhasil disimpan! Periksa selisih dan sesuaikan jika perlu.');
    }

    public function adjust(StokOpname $stokOpname)
    {
        if ($stokOpname->is_adjusted) {
            return back()->with('error', 'Stock opname ini sudah disesuaikan sebelumnya.');
        }

        // Update stock gudang sesuai qty_fisik
        $stokGudang = StokGudang::where('item_id', $stokOpname->item_id)->first();
        
        if ($stokGudang) {
            $stokGudang->update(['qty' => $stokOpname->qty_fisik]);
        }

        $stokOpname->update(['is_adjusted' => true]);

        return back()->with('success', 'Stok gudang berhasil disesuaikan dengan hasil opname.');
    }

    public function destroy(StokOpname $stokOpname)
    {
        $stokOpname->delete();

        return redirect()->route('stok-opname.index')
            ->with('success', 'Data opname berhasil dihapus.');
    }
}
