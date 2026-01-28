@extends('layouts.app')

@section('page-title', 'Distribusi Stok')
@section('page-subtitle', 'Kirim stok dari gudang ke warung')

@section('content')
<div class="max-w-3xl" x-data="distribusiForm()">
    <div class="card">
        <form action="{{ route('distribusi.store') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    @foreach($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="warung_id" class="form-label">Pilih Warung <span class="text-red-500">*</span></label>
                        <select name="warung_id" id="warung_id" class="form-select" required>
                            <option value="">-- Pilih Warung --</option>
                            @foreach($warungs as $warung)
                                <option value="{{ $warung->id }}">{{ $warung->nama_warung }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tanggal_distribusi" class="form-label">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_distribusi" id="tanggal_distribusi" class="form-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="border-t border-secondary-300 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-text-primary">📦 Barang yang Didistribusi</h3>
                        <button type="button" @click="addItem()" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                            + Tambah Barang
                        </button>
                    </div>
                    
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="grid grid-cols-12 gap-3 mb-3 items-end">
                            <div class="col-span-5">
                                <label class="form-label text-sm" x-show="index === 0">Barang</label>
                                <select :name="'items[' + index + '][item_id]'" class="form-select" x-model="row.item_id" @change="updateStock(index)" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" data-stok="{{ $item->stokGudang?->qty ?? 0 }}" data-satuan="{{ $item->satuan }}">
                                            {{ $item->nama_item }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2 text-center">
                                <label class="form-label text-sm" x-show="index === 0">Stok</label>
                                <p class="py-2.5 text-sm font-medium" x-text="row.stok + ' ' + row.satuan"></p>
                            </div>
                            <div class="col-span-3">
                                <label class="form-label text-sm" x-show="index === 0">Qty</label>
                                <input type="number" :name="'items[' + index + '][qty]'" class="form-input" x-model.number="row.qty" :max="row.stok" min="1" required>
                            </div>
                            <div class="col-span-2">
                                <button type="button" @click="removeItem(index)" class="btn btn-secondary w-full" x-show="rows.length > 1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div>
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="2" class="form-input"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('distribusi.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Proses Distribusi</button>
            </div>
        </form>
    </div>
</div>

<script>
function distribusiForm() {
    const itemsData = @json($items->map(fn($i) => ['id' => $i->id, 'stok' => $i->stokGudang?->qty ?? 0, 'satuan' => $i->satuan]));
    
    return {
        rows: [{ item_id: '', qty: 0, stok: 0, satuan: '' }],
        
        addItem() {
            this.rows.push({ item_id: '', qty: 0, stok: 0, satuan: '' });
        },
        
        removeItem(index) {
            this.rows.splice(index, 1);
        },
        
        updateStock(index) {
            const item = itemsData.find(i => i.id == this.rows[index].item_id);
            if (item) {
                this.rows[index].stok = item.stok;
                this.rows[index].satuan = item.satuan;
            }
        }
    }
}
</script>
@endsection
