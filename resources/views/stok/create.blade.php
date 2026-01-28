@extends('layouts.app')

@section('page-title', 'Tambah Barang')
@section('page-subtitle', 'Input barang baru ke stok')

@section('content')
<div class="max-w-lg">
    <div class="card">
        <form action="{{ route('stok.store') }}" method="POST">
            @csrf
            
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
                    <input type="text" name="nama_item" id="nama_item" class="form-input" value="{{ old('nama_item') }}" placeholder="Contoh: Dimsum Ayam" required autofocus>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="satuan" class="form-label">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="satuan" id="satuan" class="form-input" value="{{ old('satuan', 'pcs') }}" placeholder="pcs, kg, dus" required>
                    </div>
                    <div>
                        <label for="harga" class="form-label">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="harga" id="harga" class="form-input" value="{{ old('harga', 0) }}" min="0" required>
                    </div>
                </div>

                <div>
                    <label for="stok" class="form-label">Stok Awal <span class="text-red-500">*</span></label>
                    <input type="number" name="stok" id="stok" class="form-input" value="{{ old('stok', 0) }}" min="0" required>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('stok.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
