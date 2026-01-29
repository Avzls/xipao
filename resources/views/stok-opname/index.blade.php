@extends('layouts.app')

@section('page-title', 'Stock Opname')
@section('page-subtitle', 'Rekonsiliasi stok besar')

@section('header-actions')
    <a href="{{ route('stok-opname.create') }}" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Opname Baru
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="form-label">Barang</label>
                <select name="item_id" class="form-select">
                    <option value="">Semua Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_item }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="sesuai" {{ request('status') === 'sesuai' ? 'selected' : '' }}>✅ Sesuai</option>
                    <option value="kurang" {{ request('status') === 'kurang' ? 'selected' : '' }}>❌ Kurang</option>
                    <option value="lebih" {{ request('status') === 'lebih' ? 'selected' : '' }}>⚠️ Lebih</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th class="text-right">Stok Sistem</th>
                        <th class="text-right">Stok Fisik</th>
                        <th class="text-right">Selisih</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opnames as $opname)
                        <tr>
                            <td>{{ $opname->tanggal_opname->translatedFormat('d M Y') }}</td>
                            <td class="font-medium">{{ $opname->item->nama_item }}</td>
                            <td class="text-right">{{ number_format($opname->qty_sistem, 0, ',', '.') }}</td>
                            <td class="text-right font-semibold">{{ number_format($opname->qty_fisik, 0, ',', '.') }}</td>
                            <td class="text-right font-bold {{ $opname->selisih < 0 ? 'text-red-600' : ($opname->selisih > 0 ? 'text-amber-600' : 'text-emerald-600') }}">
                                {{ $opname->selisih >= 0 ? '+' : '' }}{{ number_format($opname->selisih, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $opname->status_badge }}">{{ $opname->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if(!$opname->is_adjusted && $opname->selisih != 0)
                                        <form action="{{ route('stok-opname.adjust', $opname) }}" method="POST" class="inline" onsubmit="event.preventDefault(); showConfirm({type:'warning', title:'Sesuaikan Stok?', message:'Stok besar akan disesuaikan dengan hasil opname.', confirmText:'Ya, Sesuaikan'}).then(ok => ok && this.submit())">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-800" title="Sesuaikan Stok">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @elseif($opname->is_adjusted)
                                        <span class="text-xs text-text-secondary">Disesuaikan</span>
                                    @endif
                                    <form action="{{ route('stok-opname.destroy', $opname) }}" method="POST" class="inline" onsubmit="event.preventDefault(); handleDelete(this, 'data opname ini')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
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
                            <td colspan="7" class="text-center py-8 text-text-secondary">
                                Belum ada data stock opname
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $opnames->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
