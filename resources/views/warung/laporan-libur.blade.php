@extends('layouts.app')

@section('page-title', 'Laporan Libur Warung')
@section('page-subtitle', 'Rekap jadwal libur per warung')

@section('content')
<div class="card">
    <!-- Filter -->
    <form action="{{ route('warung.laporan-libur') }}" method="GET" class="mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[180px]">
                <label class="form-label">Warung</label>
                <select name="warung_id" class="form-select">
                    <option value="">Semua Warung</option>
                    @foreach($warungs as $warung)
                        <option value="{{ $warung->id }}" {{ request('warung_id') == $warung->id ? 'selected' : '' }}>{{ $warung->nama_warung }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="from" class="form-input" value="{{ request('from') }}">
            </div>
            <div>
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="to" class="form-input" value="{{ request('to') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('warung.laporan-libur') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    @if($summary->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        @foreach($summary as $s)
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $s->total_libur }}</div>
            <div class="text-sm text-orange-700">{{ $s->warung->nama_warung ?? '-' }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-secondary-100 text-text-secondary text-sm">
                    <th class="px-4 py-3 text-center rounded-l-lg">Tanggal</th>
                    <th class="px-4 py-3 text-center">Hari</th>
                    <th class="px-4 py-3 text-center">Warung</th>
                    <th class="px-4 py-3 text-center rounded-r-lg">Alasan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-200">
                @forelse($liburs as $libur)
                <tr class="hover:bg-secondary-50 transition-colors">
                    <td class="px-4 py-3 text-center">{{ $libur->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center">{{ $libur->tanggal->translatedFormat('l') }}</td>
                    <td class="px-4 py-3 text-center font-medium">{{ $libur->warung->nama_warung }}</td>
                    <td class="px-4 py-3 text-center text-text-secondary">{{ $libur->alasan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-text-secondary">
                        Tidak ada data libur untuk filter yang dipilih
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($liburs->hasPages())
    <div class="mt-6">
        {{ $liburs->links() }}
    </div>
    @endif
</div>
@endsection
