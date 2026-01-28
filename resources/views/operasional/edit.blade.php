@extends('layouts.app')

@section('page-title', 'Edit Biaya Operasional')
@section('page-subtitle', 'Ubah data pengeluaran')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('operasional.update', $operasional) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="form-label">Warung <span class="text-red-500">*</span></label>
                    <select name="warung_id" class="form-select @error('warung_id') border-red-500 @enderror" required>
                        <option value="">Pilih Warung</option>
                        @foreach($warungs as $warung)
                            <option value="{{ $warung->id }}" {{ old('warung_id', $operasional->warung_id) == $warung->id ? 'selected' : '' }}>{{ $warung->nama_warung }}</option>
                        @endforeach
                    </select>
                    @error('warung_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $operasional->tanggal->format('Y-m-d')) }}" class="form-input @error('tanggal') border-red-500 @enderror" required>
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Jenis Pengeluaran <span class="text-red-500">*</span></label>
                    <select name="jenis_pengeluaran" class="form-select @error('jenis_pengeluaran') border-red-500 @enderror" required>
                        <option value="">Pilih Jenis</option>
                        @foreach($jenisOptions as $key => $label)
                            <option value="{{ $key }}" {{ old('jenis_pengeluaran', $operasional->jenis_pengeluaran) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenis_pengeluaran')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Nominal <span class="text-red-500">*</span></label>
                    <input type="number" name="nominal" value="{{ old('nominal', $operasional->nominal) }}" class="form-input @error('nominal') border-red-500 @enderror" min="0" required>
                    @error('nominal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-input" rows="2" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan', $operasional->keterangan) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('operasional.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
