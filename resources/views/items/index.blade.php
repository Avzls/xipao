@extends('layouts.app')

@section('page-title', 'Data Barang')
@section('page-subtitle', 'Master data barang')

@section('header-actions')
    <a href="{{ route('items.create') }}" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Tambah Barang
    </a>
@endsection

@section('content')
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th class="w-12">No</th>
                    <th>Nama Barang</th>
                    <th class="w-20">Satuan</th>
                    <th class="w-28 text-center">Kategori</th>
                    <th class="w-32 text-right">Harga Modal</th>
                    <th class="w-32 text-right">Harga Jual</th>
                    <th class="w-32 text-right">Stok Besar</th>
                    <th class="w-24 text-center">Status</th>
                    <th class="w-24 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="font-medium">{{ $item->nama_item }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td class="text-center">
                            <span class="badge {{ $item->kategori === 'produk' ? 'badge-info' : ($item->kategori === 'operasional' ? 'badge-warning' : 'badge-success') }}">
                                {{ ucfirst($item->kategori) }}
                            </span>
                        </td>
                        <td class="text-right">Rp {{ number_format($item->harga_modal, 0, ',', '.') }}</td>
                        <td class="text-right font-semibold text-emerald-600">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->stokGudang?->qty ?? 0, 0, ',', '.') }} {{ $item->satuan }}</td>
                        <td class="text-center">
                            @if($item->is_low_stock)
                                <span class="badge badge-danger animate-pulse-slow">Menipis!</span>
                            @else
                                <span class="badge badge-success">Aman</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('items.edit', $item) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus barang ini?')">
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
                        <td colspan="9" class="text-center py-8 text-text-secondary">
                            Belum ada data barang
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
