@extends('layouts.app')

@section('page-title', 'Stock Opname')
@section('page-subtitle', 'Sesuaikan stok dengan perhitungan fisik')

@section('content')
<style>
    .opname-layout { display: flex; flex-direction: column; gap: 1.5rem; }
    .opname-table { width: 100%; }
    .opname-sidebar { width: 100%; }
    @media (min-width: 1024px) {
        .opname-layout { flex-direction: row; }
        .opname-table { flex: 0 0 60%; max-width: 60%; }
        .opname-sidebar { flex: 0 0 38%; max-width: 38%; }
    }
</style>
<form action="{{ route('stok.opname.store') }}" method="POST" x-data="opnameForm()">
    @csrf
    <div class="opname-layout">
        <!-- Left: Table -->
        <div class="opname-table">
            <div class="card">
                <div class="table-container">
                    <table class="table text-sm">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Stok Sistem</th>
                                <th class="text-center w-28">Stok Fisik</th>
                                <th class="text-right">Terjual</th>
                                <th class="text-right">Expected Cash</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $item)
                                <tr>
                                    <td class="font-medium">
                                        {{ $item->nama_item }}
                                        <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][harga]" value="{{ $item->harga }}">
                                    </td>
                                    <td class="text-right text-emerald-600">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($item->stokGudang->qty ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <input 
                                            type="number" 
                                            name="items[{{ $index }}][qty_fisik]" 
                                            class="form-input text-sm text-center w-full" 
                                            value="{{ $item->stokGudang->qty ?? 0 }}"
                                            x-model.number="fisik[{{ $index }}]"
                                            min="0" 
                                            required
                                        >
                                    </td>
                                    <td class="text-right font-bold"
                                        :class="({{ $item->stokGudang->qty ?? 0 }} - fisik[{{ $index }}]) > 0 ? 'text-emerald-600' : 'text-text-secondary'"
                                        x-text="Math.max(0, {{ $item->stokGudang->qty ?? 0 }} - fisik[{{ $index }}])">
                                    </td>
                                    <td class="text-right font-semibold text-emerald-600"
                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, {{ $item->stokGudang->qty ?? 0 }} - fisik[{{ $index }}]) * {{ $item->harga }})">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-secondary-100">
                            <tr>
                                <td colspan="5" class="text-left font-bold">Total Expected Cash:</td>
                                <td class="text-right font-bold text-lg text-emerald-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalExpected)"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="opname-sidebar">
            <div class="space-y-4" style="position: sticky; top: 1.5rem;">
                <!-- Tanggal -->
                <div class="card">
                    <label for="tanggal_opname" class="form-label">Tanggal Opname <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_opname" id="tanggal_opname" class="form-input w-full" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Rekonsiliasi Kas -->
                <div class="card">
                    <h3 class="font-semibold mb-4 flex items-center gap-2">
                        <span class="text-xl">💰</span> Rekonsiliasi Kas
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-secondary-200">
                            <span class="text-text-secondary">Expected Cash</span>
                            <span class="font-bold text-emerald-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalExpected)"></span>
                        </div>
                        <input type="hidden" name="expected_cash" :value="totalExpected">
                        
                        <div>
                            <label class="form-label">Cash Aktual <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 text-text-secondary bg-secondary-100 border border-r-0 border-secondary-300 rounded-l-lg">Rp</span>
                                <input type="number" name="actual_cash" x-model.number="actualCash" class="form-input w-full rounded-l-none" min="0" required placeholder="0">
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center py-3 px-3 rounded-lg" 
                            :class="cashSelisih >= 0 ? 'bg-emerald-50' : 'bg-red-50'">
                            <span class="font-medium">Selisih</span>
                            <span class="font-bold text-lg" 
                                :class="cashSelisih >= 0 ? 'text-emerald-600' : 'text-red-600'"
                                x-text="(cashSelisih >= 0 ? '+' : '') + 'Rp ' + new Intl.NumberFormat('id-ID').format(cashSelisih)">
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="btn btn-primary w-full justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan & Sesuaikan Stok
                    </button>
                    <a href="{{ route('stok.index') }}" class="btn btn-secondary w-full justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </a>
                    <p class="text-xs text-text-secondary text-center">Pastikan stok fisik sesuai sebelum menyimpan</p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function opnameForm() {
    return {
        fisik: {
            @foreach($items as $index => $item)
                {{ $index }}: {{ $item->stokGudang->qty ?? 0 }},
            @endforeach
        },
        harga: {
            @foreach($items as $index => $item)
                {{ $index }}: {{ $item->harga }},
            @endforeach
        },
        sistem: {
            @foreach($items as $index => $item)
                {{ $index }}: {{ $item->stokGudang->qty ?? 0 }},
            @endforeach
        },
        actualCash: 0,
        get totalExpected() {
            let total = 0;
            for (let i in this.fisik) {
                const terjual = Math.max(0, this.sistem[i] - this.fisik[i]);
                total += terjual * this.harga[i];
            }
            return total;
        },
        get cashSelisih() {
            return this.actualCash - this.totalExpected;
        }
    }
}
</script>
@endsection
