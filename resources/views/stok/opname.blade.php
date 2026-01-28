@extends('layouts.app')

@section('page-title', 'Stock Opname')
@section('page-subtitle', 'Sesuaikan stok dengan perhitungan fisik')

@section('content')
<div class="max-w-3xl" x-data="opnameForm()">
    <div class="card">
        <form action="{{ route('stok.opname.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="tanggal_opname" class="form-label">Tanggal Opname <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_opname" id="tanggal_opname" class="form-input max-w-xs" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="table-container">
                <table class="table text-sm">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th class="text-right w-28">Stok Sistem</th>
                            <th class="text-right w-28">Stok Fisik</th>
                            <th class="text-right w-28">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td class="font-medium">
                                    {{ $item->nama_item }}
                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                </td>
                                <td class="text-right" x-data="{ sistem: {{ $item->stokGudang->qty ?? 0 }} }">
                                    <span x-text="sistem"></span>
                                </td>
                                <td class="text-right">
                                    <input 
                                        type="number" 
                                        name="items[{{ $index }}][qty_fisik]" 
                                        class="form-input text-sm text-right w-24 p-1" 
                                        value="{{ $item->stokGudang->qty ?? 0 }}"
                                        x-model.number="fisik[{{ $index }}]"
                                        min="0" 
                                        required
                                    >
                                </td>
                                <td class="text-right font-bold" 
                                    :class="(fisik[{{ $index }}] - {{ $item->stokGudang->qty ?? 0 }}) < 0 ? 'text-red-600' : ((fisik[{{ $index }}] - {{ $item->stokGudang->qty ?? 0 }}) > 0 ? 'text-amber-600' : 'text-emerald-600')"
                                    x-text="((fisik[{{ $index }}] - {{ $item->stokGudang->qty ?? 0 }}) >= 0 ? '+' : '') + (fisik[{{ $index }}] - {{ $item->stokGudang->qty ?? 0 }})">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-secondary-300">
                <a href="{{ route('stok.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan & Sesuaikan Stok
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function opnameForm() {
    return {
        fisik: {
            @foreach($items as $index => $item)
                {{ $index }}: {{ $item->stokGudang->qty ?? 0 }},
            @endforeach
        }
    }
}
</script>
@endsection
