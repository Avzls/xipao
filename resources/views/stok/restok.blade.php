@extends('layouts.app')

@section('page-title', 'Tambah Stock')
@section('page-subtitle', 'Tambah stock untuk {{ $item->nama_item }}')

@section('content')
<div class="max-w-xl">
    <div class="card">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-800">{{ $item->nama_item }}</h3>
                    <p class="text-sm text-blue-600">Stock saat ini: <span class="font-bold">{{ number_format($item->stokGudang->qty ?? 0, 0, ',', '.') }}</span> {{ $item->satuan }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('stok.restok.store', $item) }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Qty Masuk <span class="text-red-500">*</span></label>
                    <input type="number" name="qty_masuk" class="form-input" min="1" value="{{ old('qty_masuk') }}" required autofocus>
                </div>
                <div>
                    <label class="form-label">Tanggal Masuk <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_masuk" class="form-input" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Harga Beli (Opsional)</label>
                    <input type="number" name="harga_beli" class="form-input" min="0" value="{{ old('harga_beli') }}" placeholder="0">
                </div>
                <div>
                    <label class="form-label">Supplier (Opsional)</label>
                    <input type="text" name="supplier" class="form-input" value="{{ old('supplier') }}" placeholder="Nama supplier">
                </div>
            </div>

            <div>
                <label class="form-label">Keterangan (Opsional)</label>
                <input type="text" name="keterangan" class="form-input" value="{{ old('keterangan') }}" placeholder="Catatan tambahan">
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-secondary-200">
                <a href="{{ route('stok.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Stock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
