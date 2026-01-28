@extends('layouts.app')

@section('page-title', 'Tambah Warung')
@section('page-subtitle', 'Tambah cabang warung baru')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('warung.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="nama_warung" class="form-label">Nama Warung <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_warung" id="nama_warung" class="form-input @error('nama_warung') border-red-500 @enderror" value="{{ old('nama_warung') }}" required>
                    @error('nama_warung')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="form-input">{{ old('alamat') }}</textarea>
                </div>

                <div>
                    <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('warung.index') }}" class="btn btn-secondary">Batal</a>
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
