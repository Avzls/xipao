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
                @foreach($errors->all() as $error)<p class="text-sm">{{ $error }}</p>@endforeach
            </div>
            @endif

            <div class="space-y-5">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">📋 Info Transaksi</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label text-sm">Warung</label>
                            <select name="warung_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($warungs as $w)<option value="{{ $w->id }}" {{ old('warung_id') == $w->id ? 'selected' : '' }}>{{ $w->nama_warung }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label text-sm">Tanggal</label>
                            <input type="date" name="tanggal" class="form-input" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all" :class="isTutup ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200'" @click="toggleStatus()">
                        <input type="hidden" name="status" :value="isTutup ? 'tutup' : 'buka'">
                        <div class="flex items-center gap-2">
                            <span class="text-lg" x-text="isTutup ? '🔴' : '🟢'"></span>
                            <span class="font-semibold text-sm" :class="isTutup ? 'text-red-700' : 'text-emerald-700'" x-text="isTutup ? 'WARUNG TUTUP' : 'WARUNG BUKA'"></span>
                        </div>
                        <div class="relative w-12 h-6 rounded-full transition-colors" :class="isTutup ? 'bg-red-400' : 'bg-emerald-400'">
                            <div class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 shadow transition-transform" :class="isTutup ? 'translate-x-6' : ''"></div>
                        </div>
                    </div>
                </div>

                @include('transaksi._items_section')

                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100" :class="isTutup ? 'opacity-40' : ''">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">💰 Keuangan</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label text-sm">Modal (Rp)</label>
                            <input type="number" name="modal" class="form-input bg-white" x-model.number="modal" min="0" required :disabled="isTutup">
                            <p class="text-[11px] text-gray-400 mt-1" x-show="!isTutup && totalItemsSubtotal > 0">= Total Penjualan (otomatis)</p>
                        </div>
                        <div>
                            <label class="form-label text-sm">Cash Masuk (Rp)</label>
                            <input type="number" name="cash" class="form-input bg-white" x-model.number="cash" min="0" required :disabled="isTutup">
                        </div>
                    </div>
                    <div class="mt-4 p-3 rounded-lg bg-gradient-to-r" :class="omset >= 0 ? 'from-emerald-100 to-emerald-50 border border-emerald-200' : 'from-red-100 to-red-50 border border-red-200'">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold" :class="omset >= 0 ? 'text-emerald-700' : 'text-red-700'">Omset (Cash − Modal):</span>
                            <span class="text-xl font-bold" :class="omset >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="formatRupiah(omset)"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label text-sm">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea name="keterangan" rows="2" class="form-input" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<script>
function transaksiForm() {
    return {
        availableItems: @json($itemsJson),
        modal: {{ old('modal', 0) }},
        cash: {{ old('cash', 0) }},
        isTutup: {{ old('status') == 'tutup' ? 'true' : 'false' }},
        formItems: [],
        init() {
            this.$watch('totalItemsSubtotal', (val) => { if (!this.isTutup) this.modal = val; });
        },
        toggleStatus() { this.isTutup = !this.isTutup; if (this.isTutup) { this.formItems = []; this.modal = 0; this.cash = 0; } },
        addItem() { this.formItems.push({ item_id: '', qty: 1, harga: 0, satuan: '' }); },
        removeItem(idx) { this.formItems.splice(idx, 1); },
        onItemSelect(idx) { const s = this.availableItems.find(i => i.id == this.formItems[idx].item_id); if (s) { this.formItems[idx].harga = s.harga; this.formItems[idx].satuan = s.satuan; } },
        getItemStok(id) { const i = this.availableItems.find(x => x.id == id); return i ? i.stok : null; },
        get totalItemsSubtotal() { return this.formItems.reduce((s, fi) => s + (fi.qty * fi.harga), 0); },
        get omset() { return this.cash - this.modal; },
        formatRupiah(v) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(v); },
        formatNumber(n) { return new Intl.NumberFormat('id-ID').format(n); }
    }
}
</script>
@endsection
