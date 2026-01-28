<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use Illuminate\Http\Request;

class WarungController extends Controller
{
    public function index()
    {
        $warungs = Warung::withCount('transaksiHarians')
            ->orderBy('nama_warung')
            ->get();
        return view('warung.index', compact('warungs'));
    }

    public function create()
    {
        return view('warung.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_warung' => 'required|string|max:255|unique:warungs',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Warung::create($validated);

        return redirect()->route('warung.index')
            ->with('success', 'Warung berhasil ditambahkan!');
    }

    public function edit(Warung $warung)
    {
        return view('warung.edit', compact('warung'));
    }

    public function update(Request $request, Warung $warung)
    {
        $validated = $request->validate([
            'nama_warung' => 'required|string|max:255|unique:warungs,nama_warung,' . $warung->id,
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $warung->update($validated);

        return redirect()->route('warung.index')
            ->with('success', 'Warung berhasil diperbarui!');
    }

    public function destroy(Warung $warung)
    {
        $warung->delete();

        return redirect()->route('warung.index')
            ->with('success', 'Warung berhasil dihapus!');
    }
}
