<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\WarungLibur;
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

    // Warung Libur
    public function libur()
    {
        $warungs = Warung::where('status', 'aktif')->orderBy('nama_warung')->get();
        $liburs = WarungLibur::with('warung')
            ->orderBy('tanggal', 'desc')
            ->paginate(15);
        
        return view('warung.libur', compact('warungs', 'liburs'));
    }

    public function storeLibur(Request $request)
    {
        $validated = $request->validate([
            'warung_id' => 'required|exists:warungs,id',
            'tanggal' => 'required|date',
            'alasan' => 'nullable|string|max:255',
        ]);

        WarungLibur::create($validated);

        return redirect()->route('warung.libur')
            ->with('success', 'Jadwal libur berhasil ditambahkan!');
    }

    public function destroyLibur(WarungLibur $libur)
    {
        $libur->delete();

        return redirect()->route('warung.libur')
            ->with('success', 'Jadwal libur berhasil dihapus!');
    }

    public function laporanLibur(Request $request)
    {
        $warungs = Warung::where('status', 'aktif')->orderBy('nama_warung')->get();
        
        $query = WarungLibur::with('warung');

        if ($request->filled('warung_id')) {
            $query->where('warung_id', $request->warung_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal', '<=', $request->to);
        }

        $liburs = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

        // Summary per warung
        $summary = WarungLibur::selectRaw('warung_id, COUNT(*) as total_libur')
            ->when($request->filled('from'), fn($q) => $q->whereDate('tanggal', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('tanggal', '<=', $request->to))
            ->groupBy('warung_id')
            ->with('warung')
            ->get();

        return view('warung.laporan-libur', compact('warungs', 'liburs', 'summary'));
    }

    public function exportLaporanLiburPdf(Request $request)
    {
        $query = WarungLibur::with('warung');

        if ($request->filled('warung_id')) {
            $query->where('warung_id', $request->warung_id);
        }
        
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        
        $query->whereDate('tanggal', '>=', $from);
        $query->whereDate('tanggal', '<=', $to);

        $liburs = $query->orderBy('tanggal', 'desc')->get();

        // Summary per warung
        $summary = WarungLibur::selectRaw('warung_id, COUNT(*) as total_libur')
            ->whereDate('tanggal', '>=', $from)
            ->whereDate('tanggal', '<=', $to)
            ->when($request->filled('warung_id'), fn($q) => $q->where('warung_id', $request->warung_id))
            ->groupBy('warung_id')
            ->with('warung')
            ->get();

        $periodeLabel = \Carbon\Carbon::parse($from)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($to)->translatedFormat('d M Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('warung.laporan-libur-pdf', compact('liburs', 'summary', 'periodeLabel'));

        return $pdf->download('laporan-libur-' . $from . '-' . $to . '.pdf');
    }
}
