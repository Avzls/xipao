@extends('layouts.app')

@section('page-title', 'History Stock Masuk')
@section('page-subtitle', 'Riwayat penambahan stok')

@section('content')
<div class="card">
    <!-- Filter -->
    <form action="{{ route('stok.history') }}" method="GET" class="mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-input" placeholder="Nama barang / supplier..." value="{{ request('search') }}">
            </div>
            <div>
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="from" class="form-input" value="{{ request('from') }}">
            </div>
            <div>
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="to" class="form-input" value="{{ request('to') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('stok.history') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-secondary-100 text-text-secondary text-sm">
                    <th class="px-4 py-3 text-center rounded-l-lg">Tanggal</th>
                    <th class="px-4 py-3 text-center">Nama Barang</th>
                    <th class="px-4 py-3 text-center">Qty Masuk</th>
                    <th class="px-4 py-3 text-center">Harga Beli</th>
                    <th class="px-4 py-3 text-center">Supplier</th>
                    <th class="px-4 py-3 text-center rounded-r-lg">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-200">
                @forelse($histories as $history)
                <tr class="hover:bg-secondary-50 transition-colors">
                    <td class="px-4 py-3 text-center">{{ $history->tanggal_masuk->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center font-medium">{{ $history->item->nama_item ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            +{{ $history->qty_masuk }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-emerald-600 font-medium">{{ $history->harga_formatted }}</td>
                    <td class="px-4 py-3 text-center">{{ $history->supplier ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-text-secondary">{{ $history->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-text-secondary">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-secondary-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p>Belum ada riwayat stok masuk</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($histories->hasPages())
    <div class="mt-6">
        {{ $histories->links() }}
    </div>
    @endif
</div>
@endsection
