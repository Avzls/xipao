@extends('layouts.app')

@section('page-title', 'Operasional')
@section('page-subtitle', 'Input dan riwayat biaya operasional')

@section('content')
<div class="space-y-6" x-data="operasionalPage()">
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
                    <option value="">Semua</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-24">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ (request('tahun') ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filter
            </button>
            <button type="button" @click="showModal = true" class="btn btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah
            </button>
        </form>
    </div>

    <!-- Summary -->
    <div class="stat-card">
        <p class="stat-label">Total Biaya Bulan Ini</p>
        <p class="stat-value text-red-600">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</p>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Warung</th>
                        <th>Jenis</th>
                        <th class="text-right">Nominal</th>
                        <th>Keterangan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operasionals as $op)
                        <tr>
                            <td>{{ $op->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="font-medium">{{ $op->warung->nama_warung }}</td>
                            <td><span class="badge badge-secondary">{{ $op->jenis_label }}</span></td>
                            <td class="text-right font-semibold text-red-600">Rp {{ number_format($op->nominal, 0, ',', '.') }}</td>
                            <td class="text-text-secondary">{{ $op->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('operasional.edit', $op) }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('operasional.destroy', $op) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="handleDelete(this.closest('form'), 'biaya operasional ini')" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-text-secondary">Belum ada data biaya operasional</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $operasionals->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Modal Create -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Tambah Biaya Operasional</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('operasional.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
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

                        <div>
                            <label class="form-label">Jenis Pengeluaran *</label>
                            <select name="jenis_pengeluaran" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Nominal (Rp) *</label>
                            <input type="number" name="nominal" class="form-input" min="0" required placeholder="0">
                        </div>

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
function operasionalPage() {
    return {
        showModal: false
    }
}
</script>
@endsection
