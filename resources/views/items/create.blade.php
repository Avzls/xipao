@extends('layouts.app')

@section('page-title', 'Tambah Barang')
@section('page-subtitle', 'Tambah barang baru ke master data')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('items.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="nama_item" class="form-label">Nama Barang <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_item" id="nama_item" class="form-input" value="{{ old('nama_item') }}" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="satuan" class="form-label">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="satuan" id="satuan" class="form-input" value="{{ old('satuan') }}" placeholder="pcs, unit, pack" required>
                    </div>
                    <div>
                        <label for="kategori" class="form-label">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori" id="kategori" class="form-select" required>
                            <option value="produk" {{ old('kategori') === 'produk' ? 'selected' : '' }}>Produk</option>
                            <option value="operasional" {{ old('kategori') === 'operasional' ? 'selected' : '' }}>Operasional</option>
                            <option value="kemasan" {{ old('kategori') === 'kemasan' ? 'selected' : '' }}>Kemasan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="harga_modal" class="form-label">Harga Modal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="harga_modal" id="harga_modal" class="form-input" value="{{ old('harga_modal', 0) }}" min="0" required>
                    </div>
                    <div>
                        <label for="harga_jual" class="form-label">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="harga_jual" id="harga_jual" class="form-input" value="{{ old('harga_jual', 0) }}" min="0" required>
                    </div>
                </div>

                <div>
                    <label for="min_stock" class="form-label">Min. Stok (Alert)</label>
                    <input type="number" name="min_stock" id="min_stock" class="form-input" value="{{ old('min_stock', 0) }}" min="0">
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
