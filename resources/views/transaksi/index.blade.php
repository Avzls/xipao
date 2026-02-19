@extends('layouts.app')

@section('page-title', 'Transaksi')
@section('page-subtitle', 'Input dan riwayat transaksi harian')

@section('content')
<div class="space-y-6" x-data="transaksiPage()">

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card flex items-center justify-between">
            <div>
                <p class="stat-label">Total Omset</p>
                <p class="stat-value text-emerald-600">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</p>
            </div>
            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <div class="stat-card flex items-center justify-between">
            <div>
                <p class="stat-label">Total Modal</p>
                <p class="stat-value text-amber-600">Rp {{ number_format($summary['total_modal'], 0, ',', '.') }}</p>
            </div>
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <div class="stat-card flex items-center justify-between">
            <div>
                <p class="stat-label">Total Produk Terjual</p>
                <p class="stat-value text-blue-600">{{ number_format($summary['total_items'], 0, ',', '.') }} pcs</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card space-y-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="form-label">Warung</label>
                <select name="warung_id" class="form-select">
                    <option value="">Semua Warung</option>
                    @foreach($warungs as $warung)
                        <option value="{{ $warung->id }}" {{ request('warung_id') == $warung->id ? 'selected' : '' }}>{{ $warung->nama_warung }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-24">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
        </form>
        
        <!-- Action Buttons -->
        <div class="flex gap-3">
            <a href="{{ route('transaksi.export-pdf', ['warung_id' => request('warung_id'), 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export PDF
            </a>
            <button type="button" @click="openCreateModal()" class="btn btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Transaksi
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2 max-w-xs">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" placeholder="Cari warung..." class="form-input flex-1 border-0 focus:ring-0 px-0">
            </div>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">
                <span x-text="filteredData.length"></span> dari {{ $transaksis->total() }} transaksi
            </span>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('tanggal')">Tanggal</th>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('warung')">Warung</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Produk</th>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('omset')">Omset</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="tx in paginatedData" :key="tx.id">
                        <tr :class="tx.status === 'tutup' ? 'bg-red-50' : ''">
                            <td class="text-center" x-text="tx.tanggal_formatted"></td>
                            <td class="text-center font-medium" x-text="tx.warung"></td>
                            <td class="text-center">
                                <span :class="tx.status === 'tutup' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'" 
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                    x-text="tx.status === 'tutup' ? 'TUTUP' : 'BUKA'"></span>
                            </td>
                            <td class="text-center">
                                <template x-if="tx.status === 'tutup'">
                                    <span class="text-gray-400">-</span>
                                </template>
                                <template x-if="tx.status !== 'tutup'">
                                    <span class="text-xs" x-text="tx.items_count > 0 ? tx.items_summary : '-'"></span>
                                </template>
                            </td>
                            <td class="text-center font-semibold" :class="tx.omset >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="'Rp ' + formatNumber(tx.omset)"></td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a :href="'/transaksi/' + tx.id + '/edit'" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button @click="deleteItem(tx.id)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredData.length === 0">
                        <tr>
                            <td colspan="6" class="text-center py-8 text-text-secondary">Tidak ada data ditemukan</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-text-secondary">
                Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> - <span x-text="Math.min(currentPage * perPage, filteredData.length)"></span> dari <span x-text="filteredData.length"></span>
            </div>
            <div class="flex items-center gap-2">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1 border rounded-lg disabled:opacity-50">« Prev</button>
                <template x-for="page in totalPages" :key="page">
                    <button @click="currentPage = page" :class="currentPage === page ? 'bg-primary-600 text-white' : 'bg-white hover:bg-secondary-100'" class="px-3 py-1 border rounded-lg" x-text="page"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1 border rounded-lg disabled:opacity-50">Next »</button>
            </div>
        </div>
    </div>

    <!-- Modal Create -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showModal = false">
        <div class="flex items-start justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full" @click.stop>
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b bg-gradient-to-r from-primary-600 to-primary-700 rounded-t-2xl">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tambah Transaksi
                    </h3>
                    <button @click="showModal = false" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('transaksi.store') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                        
                        @if($errors->any())
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <!-- Step 1: Warung, Tanggal, Status -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">📋 Info Transaksi</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label text-sm">Warung</label>
                                    <select name="warung_id" class="form-select" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($warungs as $warung)
                                            <option value="{{ $warung->id }}">{{ $warung->nama_warung }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label text-sm">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-input" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            
                            <!-- Status Toggle -->
                            <div class="mt-3 flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200"
                                :class="isTutup ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200'"
                                @click="toggleStatus()">
                                <input type="hidden" name="status" x-model="statusValue">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg" x-text="isTutup ? '🔴' : '🟢'"></span>
                                    <span class="font-semibold text-sm" :class="isTutup ? 'text-red-700' : 'text-emerald-700'" x-text="isTutup ? 'WARUNG TUTUP' : 'WARUNG BUKA'"></span>
                                </div>
                                <div class="relative w-12 h-6 rounded-full transition-colors duration-200"
                                    :class="isTutup ? 'bg-red-400' : 'bg-emerald-400'">
                                    <div class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 shadow transition-transform duration-200"
                                        :class="isTutup ? 'translate-x-6' : 'translate-x-0'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Produk -->
                        <div class="rounded-xl border-2 transition-all duration-200" :class="isTutup ? 'border-gray-200 bg-gray-50 opacity-40' : 'border-blue-200 bg-blue-50/50'">
                            <div class="flex items-center justify-between px-4 py-3 border-b" :class="isTutup ? 'border-gray-200' : 'border-blue-200'">
                                <p class="font-semibold text-sm" :class="isTutup ? 'text-gray-400' : 'text-blue-800'">📦 Produk Terjual</p>
                                <button type="button" @click="addItem()" :disabled="isTutup" 
                                    class="flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg font-semibold transition-all"
                                    :class="isTutup ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                                    Tambah
                                </button>
                            </div>
                            
                            <div class="p-4 space-y-2">
                                <template x-if="formItems.length === 0 && !isTutup">
                                    <div class="text-center py-6">
                                        <p class="text-gray-400 text-sm">Belum ada produk</p>
                                        <p class="text-gray-300 text-xs mt-1">Klik "Tambah" untuk menambah produk</p>
                                    </div>
                                </template>
                                
                                <template x-for="(fi, idx) in formItems" :key="idx">
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex items-center gap-3">
                                            <!-- Item Number -->
                                            <div class="w-7 h-7 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" x-text="idx + 1"></div>
                                            
                                            <!-- Product Select -->
                                            <div class="flex-1 min-w-0">
                                                <select :name="'items['+idx+'][item_id]'" x-model="fi.item_id" @change="onItemSelect(idx)" 
                                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:border-blue-400 focus:ring-1 focus:ring-blue-400" :disabled="isTutup" required>
                                                    <option value="">Pilih produk...</option>
                                                    <template x-for="item in availableItems" :key="item.id">
                                                        <option :value="item.id" x-text="item.nama"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            
                                            <!-- Qty -->
                                            <div class="w-20 flex-shrink-0">
                                                <input type="number" :name="'items['+idx+'][qty]'" x-model.number="fi.qty" min="1" placeholder="Qty"
                                                    class="w-full text-sm text-center border border-gray-200 rounded-lg px-2 py-2 focus:border-blue-400 focus:ring-1 focus:ring-blue-400" :disabled="isTutup" required>
                                            </div>
                                            
                                            <!-- Subtotal -->
                                            <div class="w-28 flex-shrink-0 text-right">
                                                <p class="text-sm font-bold text-blue-700" x-text="formatRupiah(fi.qty * fi.harga)"></p>
                                                <p class="text-[10px] text-gray-400" x-show="fi.harga > 0" x-text="'@' + formatRupiah(fi.harga) + '/' + fi.satuan"></p>
                                            </div>
                                            
                                            <!-- Delete -->
                                            <button type="button" @click="removeItem(idx)" class="flex-shrink-0 w-7 h-7 rounded-full bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <!-- Stock Warning -->
                                        <template x-if="fi.item_id && getItemStok(fi.item_id) !== null">
                                            <p class="text-[11px] mt-1.5 ml-10" :class="fi.qty > getItemStok(fi.item_id) ? 'text-red-500 font-medium' : 'text-gray-400'">
                                                Stok tersedia: <span x-text="getItemStok(fi.item_id)"></span> <span x-text="fi.satuan"></span>
                                                <span x-show="fi.qty > getItemStok(fi.item_id)"> — ⚠️ Melebihi stok!</span>
                                            </p>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Total Strip -->
                            <template x-if="formItems.length > 0">
                                <div class="px-4 py-3 border-t flex justify-between items-center" :class="isTutup ? 'border-gray-200' : 'border-blue-200 bg-blue-100/50'">
                                    <span class="text-sm font-semibold" :class="isTutup ? 'text-gray-400' : 'text-blue-800'">Total Penjualan:</span>
                                    <span class="text-lg font-bold" :class="isTutup ? 'text-gray-400' : 'text-blue-700'" x-text="formatRupiah(totalItemsSubtotal)"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Step 3: Keuangan -->
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
                            
                            <!-- Omset Result -->
                            <div class="mt-4 p-3 rounded-lg bg-gradient-to-r" :class="omset >= 0 ? 'from-emerald-100 to-emerald-50 border border-emerald-200' : 'from-red-100 to-red-50 border border-red-200'">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold" :class="omset >= 0 ? 'text-emerald-700' : 'text-red-700'">Omset (Cash − Modal):</span>
                                    <span class="text-xl font-bold" :class="omset >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="formatRupiah(omset)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="form-label text-sm">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                            <textarea name="keterangan" class="form-input" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
                        <button type="button" @click="showModal = false" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function transaksiPage() {
    return {
        showModal: false,
        search: '',
        sortColumn: 'tanggal',
        sortDirection: 'desc',
        currentPage: 1,
        perPage: 10,
        data: @json($transaksiJson),
        availableItems: @json($itemsJson),
        
        // Form state
        modal: 0,
        cash: 0,
        isTutup: false,
        statusValue: 'buka',
        formItems: [],
        
        init() {
            @if($errors->any())
            this.showModal = true;
            @endif
            
            // Watch totalItemsSubtotal to auto-fill modal
            this.$watch('totalItemsSubtotal', (val) => {
                if (!this.isTutup) {
                    this.modal = val;
                }
            });
        },
        
        openCreateModal() {
            this.formItems = [];
            this.modal = 0;
            this.cash = 0;
            this.isTutup = false;
            this.statusValue = 'buka';
            this.showModal = true;
        },
        
        toggleStatus() {
            this.isTutup = !this.isTutup;
            this.statusValue = this.isTutup ? 'tutup' : 'buka';
            if (this.isTutup) {
                this.formItems = [];
                this.modal = 0;
                this.cash = 0;
            }
        },
        
        addItem() {
            this.formItems.push({ item_id: '', qty: 1, harga: 0, satuan: '' });
        },
        
        removeItem(idx) {
            this.formItems.splice(idx, 1);
        },
        
        onItemSelect(idx) {
            const selected = this.availableItems.find(i => i.id == this.formItems[idx].item_id);
            if (selected) {
                this.formItems[idx].harga = selected.harga;
                this.formItems[idx].satuan = selected.satuan;
            }
        },
        
        getItemStok(itemId) {
            const item = this.availableItems.find(i => i.id == itemId);
            return item ? item.stok : null;
        },
        
        get totalItemsSubtotal() {
            return this.formItems.reduce((sum, fi) => sum + (fi.qty * fi.harga), 0);
        },
        
        get omset() {
            return this.cash - this.modal;
        },
        
        formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        },
        
        get filteredData() {
            let result = this.data;
            if (this.search) {
                const searchLower = this.search.toLowerCase();
                result = result.filter(tx => tx.warung.toLowerCase().includes(searchLower));
            }
            result = [...result].sort((a, b) => {
                let valA = a[this.sortColumn];
                let valB = b[this.sortColumn];
                if (typeof valA === 'string') { valA = valA.toLowerCase(); valB = valB.toLowerCase(); }
                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
            return result;
        },
        
        get totalPages() {
            return Math.max(1, Math.ceil(this.filteredData.length / this.perPage));
        },
        
        get paginatedData() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredData.slice(start, start + this.perPage);
        },
        
        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
            this.currentPage = 1;
        },
        
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },
        
        deleteItem(id) {
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: 'Data yang dihapus tidak bisa dikembalikan! Stok akan dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/transaksi/' + id;
                    form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    }
}
</script>
@endsection
