@extends('layouts.app')

@section('page-title', 'Edit Barang')
@section('page-subtitle', 'Update informasi barang')

@section('content')
<div class="max-w-lg">
    <div class="card">
        <form action="{{ route('stok.update', $stok) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    @foreach($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="nama_item" class="form-label">Nama Barang <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_item" id="nama_item" class="form-input" value="{{ old('nama_item', $stok->nama_item) }}" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="satuan" class="form-label">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="satuan" id="satuan" class="form-input" value="{{ old('satuan', $stok->satuan) }}" required>
                    </div>
                    <div>
                        <label for="harga" class="form-label">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="harga" id="harga" class="form-input" value="{{ old('harga', $stok->harga) }}" min="0" required>
                    </div>
                </div>

                <div>
                    <label for="stok" class="form-label">Stok Sekarang <span class="text-red-500">*</span></label>
                    <input type="number" name="stok" id="stok" class="form-input" value="{{ old('stok', $stok->stokGudang->qty ?? 0) }}" min="0" required>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('stok.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
