@extends('layouts.app')

@section('page-title', 'Edit Transaksi')
@section('page-subtitle', 'Update data transaksi')

@section('content')
<div class="max-w-2xl" x-data="transaksiForm()">
    <div class="card">
        <form action="{{ route('transaksi.update', $transaksi) }}" method="POST">
            @csrf
            @method('PUT')
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                @foreach($errors->all() as $error)<p class="text-sm">{{ $error }}</p>@endforeach
            </div>
            @endif

            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mb-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">📋 Info Transaksi</p>
                <div class="flex items-center gap-4 text-sm">
                    <span><strong>Warung:</strong> {{ $transaksi->warung->nama_warung }}</span>
                    <span><strong>Tanggal:</strong> {{ $transaksi->tanggal->translatedFormat('d F Y') }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ ($transaksi->status ?? 'buka') === 'tutup' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ strtoupper($transaksi->status ?? 'buka') }}
                    </span>
                </div>
            </div>

            <div class="space-y-5">
                @include('transaksi._items_section')

                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">💰 Keuangan</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label text-sm">Modal (Rp)</label>
                            <input type="number" name="modal" class="form-input bg-white" x-model.number="modal" min="0" required>
                            <p class="text-[11px] text-gray-400 mt-1" x-show="totalItemsSubtotal > 0">= Total Penjualan (otomatis)</p>
                        </div>
                        <div>
                            <label class="form-label text-sm">Cash Masuk (Rp)</label>
                            <input type="number" name="cash" class="form-input bg-white" x-model.number="cash" min="0" required>
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
                    <textarea name="keterangan" rows="2" class="form-input">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update Transaksi</button>
            </div>
        </form>
    </div>
</div>

<script>
function transaksiForm() {
    return {
        availableItems: @json($itemsJson),
        modal: {{ old('modal', $transaksi->modal) }},
        cash: {{ old('cash', $transaksi->cash) }},
        isTutup: {{ ($transaksi->status ?? 'buka') === 'tutup' ? 'true' : 'false' }},
        formItems: @json($existingItemsJson).map(i => ({ item_id: String(i.item_id), qty: i.qty, harga: i.harga, satuan: i.satuan })),
        init() {
            this.$watch('totalItemsSubtotal', (val) => { this.modal = val; });
        },
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
