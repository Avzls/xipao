@extends('layouts.app')

@section('page-title', 'History Distribusi')
@section('page-subtitle', 'Riwayat distribusi stok ke warung')

@section('header-actions')
    <a href="{{ route('distribusi.create') }}" class="btn btn-primary">
        + Distribusi Baru
    </a>
@endsection

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
                <label class="form-label">Barang</label>
                <select name="item_id" class="form-select">
                    <option value="">Semua Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_item }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Warung</th>
                        <th>Barang</th>
                        <th class="text-right">Qty</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distribusis as $dist)
                        <tr>
                            <td>{{ $dist->tanggal_distribusi->translatedFormat('d M Y') }}</td>
                            <td class="font-medium">{{ $dist->warung->nama_warung }}</td>
                            <td>{{ $dist->item->nama_item }}</td>
                            <td class="text-right font-semibold text-amber-600">-{{ number_format($dist->qty_distribusi, 0, ',', '.') }} {{ $dist->item->satuan }}</td>
                            <td class="text-text-secondary">{{ $dist->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-text-secondary">Belum ada history distribusi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $distribusis->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
