@extends('layouts.app')

@section('page-title', 'Laporan Transaksi')
@section('page-subtitle', 'Riwayat transaksi harian')

@section('content')
<div class="space-y-6" x-data="transaksiTable()">
    <!-- Filters -->
    <div class="card">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[120px]">
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
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filter
            </button>
        </form>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="stat-label">Total Omset</p>
            <p class="stat-value text-emerald-600">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Modal</p>
            <p class="stat-value text-amber-600">Rp {{ number_format($summary['total_modal'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Dimsum</p>
            <p class="stat-value text-blue-600">{{ number_format($summary['total_dimsum'], 0, ',', '.') }} pcs</p>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <!-- Search Box -->
        <div class="mb-4 flex items-center gap-4">
            <div class="flex-1 max-w-xs">
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Cari warung..." 
                    class="form-input w-full"
                >
            </div>
            <div class="text-sm text-text-secondary">
                <span x-text="filteredData.length"></span> dari {{ $transaksis->total() }} transaksi
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('tanggal')">
                            <div class="flex items-center justify-center gap-1">
                                Tanggal
                                <template x-if="sortColumn === 'tanggal'">
                                    <svg class="w-4 h-4" :class="{'rotate-180': sortDirection === 'desc'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </template>
                            </div>
                        </th>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('warung')">
                            <div class="flex items-center justify-center gap-1">
                                Warung
                                <template x-if="sortColumn === 'warung'">
                                    <svg class="w-4 h-4" :class="{'rotate-180': sortDirection === 'desc'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </template>
                            </div>
                        </th>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('dimsum')">
                            <div class="flex items-center justify-center gap-1">
                                Dimsum
                                <template x-if="sortColumn === 'dimsum'">
                                    <svg class="w-4 h-4" :class="{'rotate-180': sortDirection === 'desc'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </template>
                            </div>
                        </th>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('cash')">
                            <div class="flex items-center justify-center gap-1">
                                Cash
                                <template x-if="sortColumn === 'cash'">
                                    <svg class="w-4 h-4" :class="{'rotate-180': sortDirection === 'desc'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </template>
                            </div>
                        </th>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('modal')">
                            <div class="flex items-center justify-center gap-1">
                                Modal
                                <template x-if="sortColumn === 'modal'">
                                    <svg class="w-4 h-4" :class="{'rotate-180': sortDirection === 'desc'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </template>
                            </div>
                        </th>
                        <th class="text-center cursor-pointer hover:bg-primary-700" @click="sortBy('omset')">
                            <div class="flex items-center justify-center gap-1">
                                Omset
                                <template x-if="sortColumn === 'omset'">
                                    <svg class="w-4 h-4" :class="{'rotate-180': sortDirection === 'desc'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </template>
                            </div>
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="tx in paginatedData" :key="tx.id">
                        <tr>
                            <td class="text-center" x-text="tx.tanggal_formatted"></td>
                            <td class="text-center font-medium" x-text="tx.warung"></td>
                            <td class="text-center" x-text="formatNumber(tx.dimsum)"></td>
                            <td class="text-center" x-text="'Rp ' + formatNumber(tx.cash)"></td>
                            <td class="text-center" x-text="'Rp ' + formatNumber(tx.modal)"></td>
                            <td class="text-center font-semibold text-emerald-600" x-text="'Rp ' + formatNumber(tx.omset)"></td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a :href="'/transaksi/' + tx.id + '/edit'" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form :action="'/transaksi/' + tx.id" method="POST" class="inline" @submit.prevent="deleteItem(tx.id)">
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredData.length === 0">
                        <tr>
                            <td colspan="7" class="text-center py-8 text-text-secondary">Tidak ada data ditemukan</td>
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
                <button 
                    @click="currentPage = Math.max(1, currentPage - 1)" 
                    :disabled="currentPage === 1"
                    class="px-3 py-1 border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-secondary-100"
                >
                    &laquo; Prev
                </button>
                <template x-for="page in totalPages" :key="page">
                    <button 
                        @click="currentPage = page"
                        :class="currentPage === page ? 'bg-primary-600 text-white' : 'bg-white hover:bg-secondary-100'"
                        class="px-3 py-1 border rounded-lg"
                        x-text="page"
                    ></button>
                </template>
                <button 
                    @click="currentPage = Math.min(totalPages, currentPage + 1)" 
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1 border rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-secondary-100"
                >
                    Next &raquo;
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function transaksiTable() {
    return {
        search: '',
        sortColumn: 'tanggal',
        sortDirection: 'desc',
        currentPage: 1,
        perPage: 10,
        data: @json($transaksiJson),
        
        get filteredData() {
            let result = this.data;
            
            // Filter by search
            if (this.search) {
                const searchLower = this.search.toLowerCase();
                result = result.filter(tx => tx.warung.toLowerCase().includes(searchLower));
            }
            
            // Sort
            result = [...result].sort((a, b) => {
                let valA = a[this.sortColumn];
                let valB = b[this.sortColumn];
                
                if (typeof valA === 'string') {
                    valA = valA.toLowerCase();
                    valB = valB.toLowerCase();
                }
                
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
            const end = start + this.perPage;
            return this.filteredData.slice(start, end);
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
            if (confirm('Yakin hapus transaksi ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/transaksi/' + id;
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                
                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}
</script>
@endsection
