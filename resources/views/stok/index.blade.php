@extends('layouts.app')

@section('page-title', 'Stock Besar')
@section('page-subtitle', 'Kelola barang dan stok')

@section('header-actions')
    <a href="{{ route('stok.create') }}" class="btn btn-primary text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span class="hidden sm:inline">Tambah Barang</span>
    </a>
    <a href="{{ route('stok.opname') }}" class="btn btn-secondary text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span class="hidden sm:inline">Stock Opname</span>
    </a>
@endsection

@section('content')
<!-- Daftar Barang -->
<div class="card mb-6">
    <h3 class="font-semibold text-lg mb-4">📦 Daftar Barang</h3>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Last Opname</th>
                    <th class="text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="text-center font-medium">{{ $item->nama_item }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        <td class="text-center text-emerald-600 font-semibold">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-center font-bold {{ ($item->stokGudang->qty ?? 0) < 10 ? 'text-red-600' : 'text-text-primary' }}">
                            {{ number_format($item->stokGudang->qty ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($item->latestOpname)
                                <span class="badge {{ $item->latestOpname->status_badge }}">
                                    {{ $item->latestOpname->tanggal_opname->translatedFormat('d M Y') }}
                                </span>
                            @else
                                <span class="text-text-secondary text-sm">Belum pernah</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="openRestokModal({{ $item->id }}, '{{ $item->nama_item }}', {{ $item->stokGudang->qty ?? 0 }}, '{{ $item->satuan }}')" class="text-green-600 hover:text-green-800" title="Tambah Stock">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </button>
                                <a href="{{ route('stok.edit', $item) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('stok.destroy', $item) }}" method="POST" class="inline" onsubmit="event.preventDefault(); handleDelete(this, '{{ $item->nama_item }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
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
                        <td colspan="6" class="text-center py-8 text-text-secondary">
                            Belum ada data barang. <a href="{{ route('stok.create') }}" class="text-primary-600 hover:underline">Tambah sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- History Stock Masuk -->
<div class="card">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <h3 class="font-semibold text-lg">📋 History Stock Masuk</h3>
        <form action="{{ route('stok.index') }}" method="GET" class="flex flex-wrap items-end gap-2">
            <div>
                <label class="form-label text-sm">Dari</label>
                <input type="date" name="from" class="form-input text-sm py-1.5" value="{{ request('from') }}">
            </div>
            <div>
                <label class="form-label text-sm">Sampai</label>
                <input type="date" name="to" class="form-input text-sm py-1.5" value="{{ request('to') }}">
            </div>
            <button type="submit" class="btn btn-primary text-sm py-1.5">Filter</button>
            @if(request('from') || request('to'))
                <a href="{{ route('stok.index') }}" class="btn btn-secondary text-sm py-1.5">Reset</a>
            @endif
            <a href="{{ route('stok.history.pdf', ['from' => request('from', now()->startOfMonth()->format('Y-m-d')), 'to' => request('to', now()->format('Y-m-d'))]) }}" class="btn btn-primary text-sm py-1.5" style="background-color: #dc2626;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PDF
            </a>
        </form>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $history)
                    <tr>
                        <td class="text-center font-medium">{{ $history->item->nama_item ?? '-' }}</td>
                        <td class="text-center">{{ $history->item->satuan ?? '-' }}</td>
                        <td class="text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                +{{ number_format($history->qty_masuk, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-center">{{ $history->tanggal_masuk->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-text-secondary">
                            Belum ada riwayat stok masuk
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<!-- Modal Tambah Stock -->
<div id="restokModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="font-semibold text-lg mb-4">Tambah Stock</h3>
        <div id="restokItemInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <span class="font-medium text-blue-800" id="restokItemName">-</span>
            <span class="text-sm text-blue-600 block">Stock saat ini: <strong id="restokCurrentQty">0</strong> <span id="restokItemSatuan">pcs</span></span>
        </div>
        <form id="restokForm" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Qty Masuk <span class="text-red-500">*</span></label>
                    <input type="number" name="qty_masuk" id="restokQty" class="form-input" min="1" required autofocus>
                </div>
                <div>
                    <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_masuk" id="restokTanggal" class="form-input" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-secondary-200">
                <button type="button" onclick="closeRestokModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRestokModal(itemId, itemName, currentQty, satuan) {
    document.getElementById('restokItemName').textContent = itemName;
    document.getElementById('restokCurrentQty').textContent = currentQty.toLocaleString('id-ID');
    document.getElementById('restokItemSatuan').textContent = satuan;
    document.getElementById('restokForm').action = '/stok/' + itemId + '/restok';
    document.getElementById('restokQty').value = '';
    document.getElementById('restokModal').classList.remove('hidden');
    document.getElementById('restokModal').classList.add('flex');
    document.getElementById('restokQty').focus();
}

function closeRestokModal() {
    document.getElementById('restokModal').classList.add('hidden');
    document.getElementById('restokModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('restokModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRestokModal();
    }
});
</script>
@endsection

