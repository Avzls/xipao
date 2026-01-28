@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', now()->translatedFormat('l, d F Y'))

@section('content')
<div class="space-y-6">
    <!-- Today's Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">💰</span>
                </div>
                <div>
                    <p class="stat-label">Omset Hari Ini</p>
                    <p class="stat-value text-emerald-600">Rp {{ number_format($totalOmsetHariIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🥟</span>
                </div>
                <div>
                    <p class="stat-label">Dimsum Terjual</p>
                    <p class="stat-value text-blue-600">{{ number_format($totalDimsumHariIni, 0, ',', '.') }} pcs</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📊</span>
                </div>
                <div>
                    <p class="stat-label">Omset Bulan Ini</p>
                    <p class="stat-value text-purple-600">Rp {{ number_format($totalOmsetBulanIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🏪</span>
                </div>
                <div>
                    <p class="stat-label">Warung Aktif</p>
                    <p class="stat-value text-amber-600">{{ $warungs->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="card">
            <h3 class="card-header">🚀 Aksi Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('transaksi.create') }}" class="btn btn-primary w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Input Transaksi
                </a>
                <a href="{{ route('distribusi.create') }}" class="btn btn-secondary w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Distribusi Stok
                </a>
                <a href="{{ route('gudang.restok') }}" class="btn btn-secondary w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                    Restok Gudang
                </a>
            </div>
        </div>

        <!-- Stock Alerts -->
        <div class="card lg:col-span-2">
            <h3 class="card-header flex items-center gap-2">
                📦 Alert Stok Gudang
                @if($stokMenipis->count() > 0)
                    <span class="badge badge-danger animate-pulse-slow">{{ $stokMenipis->count() }} Item</span>
                @endif
            </h3>
            @if($stokMenipis->count() > 0)
                <div class="space-y-3">
                    @foreach($stokMenipis as $stok)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <span class="text-red-600">⚠️</span>
                                </div>
                                <div>
                                    <p class="font-medium text-text-primary">{{ $stok->item->nama_item }}</p>
                                    <p class="text-sm text-text-secondary">Min: {{ number_format($stok->min_stock, 0, ',', '.') }} {{ $stok->item->satuan }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-red-600">{{ number_format($stok->qty, 0, ',', '.') }} {{ $stok->item->satuan }}</p>
                                <a href="{{ route('gudang.restok') }}" class="text-sm text-primary-600 hover:underline">Restok →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-text-secondary">
                    <span class="text-4xl">✅</span>
                    <p class="mt-2">Semua stok dalam kondisi aman</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Today's Transactions per Warung -->
    <div class="card">
        <h3 class="card-header">📊 Ringkasan Hari Ini per Warung</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th class="text-center">Dimsum</th>
                        <th class="text-right">Cash</th>
                        <th class="text-right">Modal</th>
                        <th class="text-right">Omset</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($warungs as $warung)
                        @php
                            $tx = $hariIni->where('warung_id', $warung->id)->first();
                        @endphp
                        <tr>
                            <td class="font-medium">{{ $warung->nama_warung }}</td>
                            <td class="text-center">{{ $tx ? number_format($tx->dimsum_terjual, 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $tx ? 'Rp ' . number_format($tx->cash, 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $tx ? 'Rp ' . number_format($tx->modal, 0, ',', '.') : '-' }}</td>
                            <td class="text-right font-semibold text-emerald-600">{{ $tx ? 'Rp ' . number_format($tx->omset, 0, ',', '.') : '-' }}</td>
                            <td class="text-center">
                                @if($tx)
                                    <span class="badge badge-success">Input ✓</span>
                                @else
                                    <span class="badge badge-warning">Belum Input</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-secondary-100 font-semibold">
                        <td>TOTAL</td>
                        <td class="text-center">{{ number_format($hariIni->sum('dimsum_terjual'), 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($hariIni->sum('cash'), 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($hariIni->sum('modal'), 0, ',', '.') }}</td>
                        <td class="text-right text-emerald-600">Rp {{ number_format($hariIni->sum('omset'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Chart & Ranking -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart -->
        <div class="card">
            <h3 class="card-header">📈 Trend Omset 7 Hari Terakhir</h3>
            <canvas id="omsetChart" height="200"></canvas>
        </div>

        <!-- Ranking -->
        <div class="card">
            <h3 class="card-header">🏆 Ranking Performa Bulan Ini</h3>
            <div class="space-y-3">
                @foreach($ranking as $index => $item)
                    @php
                        $percentage = $totalOmsetRanking > 0 ? ($item['omset'] / $totalOmsetRanking) * 100 : 0;
                        $medal = match($index) {
                            0 => '🥇',
                            1 => '🥈',
                            2 => '🥉',
                            default => ($index + 1) . '.',
                        };
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-2xl w-10 text-center">{{ $medal }}</span>
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <span class="font-medium">{{ $item['warung']->nama_warung }}</span>
                                <span class="text-sm text-text-secondary">{{ number_format($percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-secondary-200 rounded-full h-2">
                                <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <p class="text-sm text-text-secondary mt-1">Rp {{ number_format($item['omset'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('omsetChart').getContext('2d');
        const chartData = @json($last7Days);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.tanggal),
                datasets: [{
                    label: 'Total Omset',
                    data: chartData.map(d => d.total),
                    borderColor: '#8B0000',
                    backgroundColor: 'rgba(139, 0, 0, 0.1)',
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
