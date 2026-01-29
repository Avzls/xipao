@extends('layouts.app')

@section('page-title', 'Input Transaksi Harian')
@section('page-subtitle', 'Catat penjualan harian per warung')

@section('content')
<div class="max-w-2xl" x-data="transaksiForm()">
    <div class="card">
        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    @foreach($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="space-y-5">
                <!-- Warung & Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="warung_id" class="form-label">Warung <span class="text-red-500">*</span></label>
                        <select name="warung_id" id="warung_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach($warungs as $warung)
                                <option value="{{ $warung->id }}" {{ old('warung_id') == $warung->id ? 'selected' : '' }}>{{ $warung->nama_warung }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tanggal" class="form-label">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" class="form-input" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <!-- Penjualan Dimsum -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-800 mb-3">🥟 Penjualan Dimsum</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="dimsum_terjual" class="form-label">Dimsum Terjual (pcs) <span class="text-red-500">*</span></label>
                            <input type="number" name="dimsum_terjual" id="dimsum_terjual" class="form-input" x-model.number="dimsum" min="0" required>
                            <p class="text-xs text-blue-600 mt-1">Stok akan otomatis berkurang</p>
                        </div>
                        <div>
                            <label class="form-label">Penjualan</label>
                            <div class="bg-white text-blue-700 font-bold px-4 py-2.5 rounded-lg border border-blue-200" x-text="formatRupiah(dimsum * harga)"></div>
                            <p class="text-xs text-blue-600 mt-1">Harga: <span x-text="formatRupiah(harga)"></span>/pcs</p>
                        </div>
                    </div>
                </div>

                <!-- Modal & Cash -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="modal" class="form-label">Modal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="modal" id="modal" class="form-input" x-model.number="modal" min="0" required>
                        <p class="text-xs text-text-secondary mt-1">
                            Klik untuk isi: <span class="text-blue-600 font-medium cursor-pointer hover:underline" @click="modal = dimsum * harga" x-text="formatRupiah(dimsum * harga)"></span>
                        </p>
                    </div>
                    <div>
                        <label for="cash" class="form-label">Cash Masuk (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="cash" id="cash" class="form-input" x-model.number="cash" min="0" required>
                        <p class="text-xs text-text-secondary mt-1">
                            Klik untuk isi: <span class="text-emerald-600 font-medium cursor-pointer hover:underline" @click="cash = dimsum * harga" x-text="formatRupiah(dimsum * harga)"></span>
                        </p>
                    </div>
                </div>

                <!-- Omset Summary -->
                <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-emerald-800">💰 Omset (Cash - Modal):</span>
                        <span class="text-2xl font-bold" :class="omset >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="formatRupiah(omset)"></span>
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" rows="2" class="form-input" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function transaksiForm() {
    return {
        harga: {{ $hargaDimsum ?? 0 }},
        dimsum: {{ old('dimsum_terjual', 0) }},
        modal: {{ old('modal', 0) }},
        cash: {{ old('cash', 0) }},
        
        init() {
            this.$watch('dimsum', (value) => {
                this.modal = value * this.harga;
                this.cash = value * this.harga;
            });
        },
        
        get penjualan() {
            return this.dimsum * this.harga;
        },
        
        get omset() {
            return this.cash - this.modal;
        },
        
        formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        }
    }
}
</script>
@endsection
