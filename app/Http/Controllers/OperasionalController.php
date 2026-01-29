<?php

namespace App\Http\Controllers;

use App\Models\PengeluaranOperasional;
use App\Models\Warung;
use Illuminate\Http\Request;

class OperasionalController extends Controller
{
    public function index(Request $request)
    {
        $query = PengeluaranOperasional::with('warung')
            ->orderBy('tanggal', 'desc');

        if ($request->filled('warung_id')) {
            $query->where('warung_id', $request->warung_id);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        } else {
            $query->whereYear('tanggal', now()->year);
        }

        $operasionals = $query->paginate(20);
        $warungs = Warung::orderBy('nama_warung')->get();
        $jenisOptions = PengeluaranOperasional::JENIS;
        
        $totalBulanIni = PengeluaranOperasional::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('nominal');

        return view('operasional.index', compact('operasionals', 'warungs', 'jenisOptions', 'totalBulanIni'));
    }

    public function create()
    {
        $warungs = Warung::orderBy('nama_warung')->get();
        $jenisOptions = PengeluaranOperasional::JENIS;
        return view('operasional.create', compact('warungs', 'jenisOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warung_id' => 'required|exists:warungs,id',
            'tanggal' => 'required|date',
            'jenis_pengeluaran' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        PengeluaranOperasional::create($request->all());

        return redirect()->route('operasional.index')
            ->with('success', 'Biaya operasional berhasil ditambahkan');
    }

    public function edit(PengeluaranOperasional $operasional)
    {
        $warungs = Warung::orderBy('nama_warung')->get();
        $jenisOptions = PengeluaranOperasional::JENIS;
        return view('operasional.edit', compact('operasional', 'warungs', 'jenisOptions'));
    }

    public function update(Request $request, PengeluaranOperasional $operasional)
    {
        $request->validate([
            'warung_id' => 'required|exists:warungs,id',
            'tanggal' => 'required|date',
            'jenis_pengeluaran' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $operasional->update($request->all());

        return redirect()->route('operasional.index')
            ->with('success', 'Biaya operasional berhasil diperbarui');
    }

    public function destroy(PengeluaranOperasional $operasional)
    {
        $operasional->delete();

        return redirect()->route('operasional.index')
            ->with('success', 'Biaya operasional berhasil dihapus');
    }
}
