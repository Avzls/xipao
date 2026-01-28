@extends('layouts.app')

@section('page-title', 'Stock Opname Baru')
@section('page-subtitle', 'Input hasil hitung fisik stok besar')

@section('content')
<div class="max-w-4xl" x-data="opnameForm()">
    <div class="card">
        <form action="{{ route('stok-opname.store') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    @foreach($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="space-y-6">
                <div class="max-w-xs">
                    <label for="tanggal_opname" class="form-label">Tanggal Opname <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_opname" id="tanggal_opname" class="form-input" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="border-t border-secondary-300 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-text-primary">📦 Daftar Barang untuk Opname</h3>
                        <button type="button" @click="addItem()" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                            + Tambah Barang
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-secondary-300">
                                    <th class="text-left py-2 px-2">Barang</th>
                                    <th class="text-right py-2 px-2 w-28">Stok Sistem</th>
                                    <th class="text-right py-2 px-2 w-28">Stok Fisik</th>
                                    <th class="text-right py-2 px-2 w-28">Selisih</th>
                                    <th class="text-left py-2 px-2 w-40">Keterangan</th>
                                    <th class="w-12"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in rows" :key="index">
                                    <tr class="border-b border-secondary-200">
                                        <td class="py-2 px-2">
                                            <select :name="'items[' + index + '][item_id]'" class="form-select text-sm" x-model="row.item_id" @change="updateSistem(index)" required>
                                                <option value="">-- Pilih --</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" data-stok="{{ $item->stokGudang?->qty ?? 0 }}">{{ $item->nama_item }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-2 px-2 text-right font-medium" x-text="row.sistem"></td>
                                        <td class="py-2 px-2">
                                            <input type="number" :name="'items[' + index + '][qty_fisik]'" class="form-input text-sm text-right w-24" x-model.number="row.fisik" @input="updateSelisih(index)" min="0" required>
                                        </td>
                                        <td class="py-2 px-2 text-right font-bold" :class="row.selisih < 0 ? 'text-red-600' : (row.selisih > 0 ? 'text-amber-600' : 'text-emerald-600')" x-text="(row.selisih >= 0 ? '+' : '') + row.selisih"></td>
                                        <td class="py-2 px-2">
                                            <input type="text" :name="'items[' + index + '][keterangan]'" class="form-input text-sm" x-model="row.keterangan" placeholder="Opsional">
                                        </td>
                                        <td class="py-2 px-2">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700" x-show="rows.length > 1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('stok-opname.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Opname
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function opnameForm() {
    const itemsData = @json($items->mapWithKeys(fn($i) => [$i->id => $i->stokGudang?->qty ?? 0]));
    
    return {
        rows: [{ item_id: '', sistem: 0, fisik: 0, selisih: 0, keterangan: '' }],
        
        addItem() {
            this.rows.push({ item_id: '', sistem: 0, fisik: 0, selisih: 0, keterangan: '' });
        },
        
        removeItem(index) {
            this.rows.splice(index, 1);
        },
        
        updateSistem(index) {
            const itemId = this.rows[index].item_id;
            this.rows[index].sistem = itemsData[itemId] || 0;
            this.rows[index].fisik = this.rows[index].sistem;
            this.updateSelisih(index);
        },
        
        updateSelisih(index) {
            this.rows[index].selisih = this.rows[index].fisik - this.rows[index].sistem;
        }
    }
}
</script>
@endsection
