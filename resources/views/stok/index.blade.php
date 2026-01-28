@extends('layouts.app')

@section('page-title', 'Stock Besar')
@section('page-subtitle', 'Kelola barang dan stok')

@section('header-actions')
    <a href="{{ route('stok.create') }}" class="btn btn-primary text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span class="hidden sm:inline">Tambah Barang</span>
    </a>
    <a href="{{ route('stok.opname') }}" class="btn btn-secondary text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span class="hidden sm:inline">Stock Opname</span>
    </a>
@endsection

@section('content')
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Last Opname</th>
                    <th class="w-24">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="font-medium">{{ $item->nama_item }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td class="text-emerald-600 font-semibold">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="font-bold {{ ($item->stokGudang->qty ?? 0) < 10 ? 'text-red-600' : 'text-text-primary' }}">
                            {{ number_format($item->stokGudang->qty ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($item->latestOpname)
                                <span class="badge {{ $item->latestOpname->status_badge }}">
                                    {{ $item->latestOpname->tanggal_opname->translatedFormat('d M Y') }}
                                </span>
                            @else
                                <span class="text-text-secondary text-sm">Belum pernah</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('stok.edit', $item) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('stok.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus barang ini?')">
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
                        <td colspan="6" class="text-center py-8 text-text-secondary">
                            Belum ada data barang. <a href="{{ route('stok.create') }}" class="text-primary-600 hover:underline">Tambah sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
