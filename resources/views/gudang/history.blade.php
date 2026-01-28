@extends('layouts.app')

@section('page-title', 'History Restok')
@section('page-subtitle', 'Riwayat restok besar')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <form method="GET" class="flex flex-wrap items-end gap-4">
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
                        <th>Barang</th>
                        <th class="text-right">Qty Masuk</th>
                        <th class="text-right">Harga Beli</th>
                        <th>Supplier</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($restoks as $restok)
                        <tr>
                            <td>{{ $restok->tanggal_masuk->translatedFormat('d M Y') }}</td>
                            <td class="font-medium">{{ $restok->item->nama_item }}</td>
                            <td class="text-right font-semibold text-emerald-600">+{{ number_format($restok->qty_masuk, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $restok->harga_beli_formatted }}</td>
                            <td>{{ $restok->supplier ?? '-' }}</td>
                            <td class="text-text-secondary">{{ $restok->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-text-secondary">Belum ada history restok</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $restoks->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
