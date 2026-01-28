@extends('layouts.app')

@section('page-title', 'Restok Barang')
@section('page-subtitle', 'Tambah stok ke gudang pusat')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('gudang.restok.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="item_id" class="form-label">Pilih Barang <span class="text-red-500">*</span></label>
                    <select name="item_id" id="item_id" class="form-select" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_item }} ({{ $item->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="qty_masuk" class="form-label">Qty Masuk <span class="text-red-500">*</span></label>
                        <input type="number" name="qty_masuk" id="qty_masuk" class="form-input" value="{{ old('qty_masuk') }}" min="1" required>
                    </div>
                    <div>
                        <label for="tanggal_masuk" class="form-label">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-input" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
                        <input type="number" name="harga_beli" id="harga_beli" class="form-input" value="{{ old('harga_beli', 0) }}" min="0">
                    </div>
                    <div>
                        <label for="supplier" class="form-label">Supplier</label>
                        <input type="text" name="supplier" id="supplier" class="form-input" value="{{ old('supplier') }}" placeholder="Nama supplier">
                    </div>
                </div>

                <div>
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="2" class="form-input">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Restok</button>
            </div>
        </form>
    </div>
</div>
@endsection
