@extends('layouts.app')

@section('page-title', 'Laporan Transaksi')
@section('page-subtitle', 'Riwayat transaksi harian')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="form-label">Warung</label>
                <select name="warung_id" class="form-select">
                    <option value="">Semua Warung</option>
                    @foreach($warungs as $warung)
                        <option value="{{ $warung->id }}" {{ request('warung_id') == $warung->id ? 'selected' : '' }}>{{ $warung->nama_warung }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('transaksi.create') }}" class="btn btn-success">+ Input Transaksi</a>
        </form>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="stat-label">Total Omset</p>
            <p class="stat-value text-emerald-600">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Modal</p>
            <p class="stat-value text-amber-600">Rp {{ number_format($summary['total_modal'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Dimsum</p>
            <p class="stat-value text-blue-600">{{ number_format($summary['total_dimsum'], 0, ',', '.') }} pcs</p>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Warung</th>
                        <th class="text-center">Dimsum</th>
                        <th class="text-right">Cash</th>
                        <th class="text-right">Modal</th>
                        <th class="text-right">Omset</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $tx)
                        <tr>
                            <td>{{ $tx->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="font-medium">{{ $tx->warung->nama_warung }}</td>
                            <td class="text-center">{{ number_format($tx->dimsum_terjual, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($tx->cash, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($tx->modal, 0, ',', '.') }}</td>
                            <td class="text-right font-semibold text-emerald-600">Rp {{ number_format($tx->omset, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('transaksi.edit', $tx) }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('transaksi.destroy', $tx) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-text-secondary">Belum ada data transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $transaksis->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
