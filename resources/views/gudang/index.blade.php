@extends('layouts.app')

@section('page-title', 'Stok Besar')
@section('page-subtitle', 'Monitoring stok besar')

@section('header-actions')
    <a href="{{ route('gudang.restok') }}" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
        </svg>
        Restok Barang
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($stoks as $stok)
        @php
            $statusColor = match($stok->status) {
                'habis' => 'border-red-500 bg-red-50',
                'menipis' => 'border-amber-500 bg-amber-50',
                default => 'border-secondary-300 bg-white',
            };
            $statusBadge = match($stok->status) {
                'habis' => 'badge-danger',
                'menipis' => 'badge-warning',
                default => 'badge-success',
            };
            $statusIcon = match($stok->status) {
                'habis' => '❌',
                'menipis' => '⚠️',
                default => '✅',
            };
        @endphp
        <div class="card {{ $statusColor }} border-l-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">{{ $statusIcon }}</span>
                    <div>
                        <h3 class="font-semibold text-text-primary">{{ $stok->item->nama_item }}</h3>
                        <span class="badge {{ $stok->item->kategori === 'produk' ? 'badge-info' : ($stok->item->kategori === 'operasional' ? 'badge-warning' : 'badge-success') }}">
                            {{ ucfirst($stok->item->kategori) }}
                        </span>
                    </div>
                </div>
                <span class="badge {{ $statusBadge }}">{{ ucfirst($stok->status) }}</span>
            </div>
            
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-text-secondary">Stok Saat Ini:</span>
                    <span class="font-bold {{ $stok->is_low ? 'text-red-600' : 'text-text-primary' }}">
                        {{ number_format($stok->qty, 0, ',', '.') }} {{ $stok->item->satuan }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-text-secondary">Min. Stok:</span>
                    <span>{{ number_format($stok->min_stock, 0, ',', '.') }} {{ $stok->item->satuan }}</span>
                </div>
                @if($stok->last_restock_date)
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">Terakhir Restok:</span>
                        <span>{{ $stok->last_restock_date->translatedFormat('d M Y') }}</span>
                    </div>
                @endif
            </div>
            
            @if($stok->is_low)
                <a href="{{ route('gudang.restok') }}" class="btn btn-primary w-full mt-4">
                    Restok Sekarang →
                </a>
            @endif
        </div>
    @endforeach
</div>
@endsection
