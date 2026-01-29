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
                <p class="stat-label">Total Dimsum</p>
                <p class="stat-value text-blue-600">{{ number_format($summary['total_dimsum'], 0, ',', '.') }} pcs</p>
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
            <button type="button" @click="showModal = true" class="btn btn-primary">
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
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('dimsum')">Dimsum</th>
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
                            <td class="text-center" x-text="tx.status === 'tutup' ? '-' : formatNumber(tx.dimsum)"></td>
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
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Tambah Transaksi</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('transaksi.store') }}" method="POST">
                    @csrf
                    
                    <!-- Validation Errors -->
                    @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-700 font-medium text-sm">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <div class="space-y-4">
                        <!-- Warung & Tanggal -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Warung *</label>
                                <select name="warung_id" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($warungs as $warung)
                                        <option value="{{ $warung->id }}">{{ $warung->nama_warung }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Tanggal *</label>
                                <input type="date" name="tanggal" class="form-input" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- Toggle Buka/Tutup -->
                        <div class="p-4 rounded-xl border-2 transition-all duration-300"
                            :class="isTutup ? 'bg-red-50 border-red-300' : 'bg-emerald-50 border-emerald-300'">
                            <input type="hidden" name="status" x-model="statusValue">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleStatus()">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors duration-300"
                                        :class="isTutup ? 'bg-red-200' : 'bg-emerald-200'">
                                        <span class="text-xl" x-text="isTutup ? '🔒' : '🔓'"></span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-base" :class="isTutup ? 'text-red-700' : 'text-emerald-700'" x-text="isTutup ? 'WARUNG TUTUP' : 'WARUNG BUKA'"></p>
                                        <p class="text-xs text-gray-500" x-text="isTutup ? 'Semua nilai akan di-set ke 0' : 'Klik untuk ubah status'"></p>
                                    </div>
                                </div>
                                <!-- Toggle Switch -->
                                <div class="relative w-14 h-7 rounded-full transition-colors duration-300 shadow-inner"
                                    :class="isTutup ? 'bg-red-500' : 'bg-emerald-500'">
                                    <div class="absolute top-1 left-1 bg-white rounded-full h-5 w-5 shadow-md transition-transform duration-300 ease-out flex items-center justify-center"
                                        :class="isTutup ? 'translate-x-7' : 'translate-x-0'">
                                        <span class="text-xs" :class="isTutup ? 'text-red-500' : 'text-emerald-500'" x-text="isTutup ? '✕' : '✓'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dimsum -->
                        <div class="p-4 rounded-lg" :class="isTutup ? 'bg-gray-100 opacity-50' : 'bg-blue-50'">
                            <label class="form-label" :class="isTutup ? 'text-gray-500' : ''">Dimsum Terjual (pcs) *</label>
                            <input type="number" name="dimsum_terjual" class="form-input" x-model.number="dimsum" :readonly="isTutup" min="0" required>
                            <p class="text-xs mt-1" :class="isTutup ? 'text-gray-400' : 'text-blue-600'">Penjualan: <span x-text="formatRupiah(dimsum * harga)"></span></p>
                        </div>

                        <!-- Modal & Cash -->
                        <div class="grid grid-cols-2 gap-4" :class="isTutup ? 'opacity-50' : ''">
                            <div>
                                <label class="form-label">Modal (Rp) *</label>
                                <input type="number" name="modal" class="form-input" x-model.number="modal" :readonly="isTutup" min="0" required>
                            </div>
                            <div>
                                <label class="form-label">Cash Masuk (Rp) *</label>
                                <input type="number" name="cash" class="form-input" x-model.number="cash" :readonly="isTutup" min="0" required>
                            </div>
                        </div>

                        <!-- Omset -->
                        <div class="p-3 rounded-lg bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-emerald-800">💰 Omset:</span>
                                <span class="text-xl font-bold" :class="omset >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="formatRupiah(omset)"></span>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-input" rows="2" placeholder="Opsional..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                        <button type="button" @click="showModal = false" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
        
        // Form state
        harga: {{ $hargaDimsum ?? 0 }},
        dimsum: 0,
        modal: 0,
        cash: 0,
        isTutup: false,
        statusValue: 'buka',
        
        init() {
            this.$watch('dimsum', (value) => {
                if (!this.isTutup) {
                    this.modal = value * this.harga;
                }
            });
            // Auto open modal if there are errors
            @if($errors->any())
            this.showModal = true;
            @endif
        },
        
        toggleStatus() {
            this.isTutup = !this.isTutup;
            this.statusValue = this.isTutup ? 'tutup' : 'buka';
            if (this.isTutup) {
                this.dimsum = 0;
                this.modal = 0;
                this.cash = 0;
            }
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
        
        async deleteItem(id) {
            const confirmed = await confirmDelete('transaksi ini');
            if (confirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/transaksi/' + id;
                form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}
</script>
@endsection
