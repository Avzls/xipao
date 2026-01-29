@extends('layouts.app')

@section('page-title', 'Jadwal Libur Warung')
@section('page-subtitle', 'Kelola jadwal libur per warung')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Input -->
    <div class="lg:col-span-1">
        <div class="card">
            <h3 class="font-semibold text-lg mb-4">Tambah Jadwal Libur</h3>
            <form action="{{ route('warung.libur.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Warung <span class="text-red-500">*</span></label>
                    <select name="warung_id" class="form-select" required>
                        <option value="">-- Pilih Warung --</option>
                        @foreach($warungs as $warung)
                            <option value="{{ $warung->id }}">{{ $warung->nama_warung }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tanggal Libur <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" class="form-input" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="form-label">Alasan (Opsional)</label>
                    <input type="text" name="alasan" class="form-input" placeholder="Contoh: Libur lebaran">
                </div>
                <button type="submit" class="btn btn-primary w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Jadwal Libur
                </button>
            </form>
        </div>
    </div>

    <!-- Table List -->
    <div class="lg:col-span-2">
        <div class="card">
            <h3 class="font-semibold text-lg mb-4">Daftar Jadwal Libur</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-secondary-100 text-text-secondary text-sm">
                            <th class="px-4 py-3 text-center rounded-l-lg">Tanggal</th>
                            <th class="px-4 py-3 text-center">Warung</th>
                            <th class="px-4 py-3 text-center">Alasan</th>
                            <th class="px-4 py-3 text-center rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-200">
                        @forelse($liburs as $libur)
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-4 py-3 text-center">{{ $libur->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ $libur->warung->nama_warung }}</td>
                            <td class="px-4 py-3 text-center text-text-secondary">{{ $libur->alasan ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('warung.libur.destroy', $libur) }}" method="POST" class="inline" onsubmit="event.preventDefault(); handleDelete(this, 'jadwal libur ini')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-text-secondary">
                                Belum ada jadwal libur
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($liburs->hasPages())
            <div class="mt-4">
                {{ $liburs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
