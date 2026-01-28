@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', now()->translatedFormat('l, d F Y'))

@section('content')
<div class="space-y-6">
    <!-- Today's Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">💸</span>
                </div>
                <div>
                    <p class="stat-label">Operasional Hari Ini</p>
                    <p class="stat-value text-red-600">Rp {{ number_format($operasionalHariIni, 0, ',', '.') }}</p>
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

    <!-- Today's Transactions per Warung -->
    <div class="card">
        <h3 class="card-header">📊 Ringkasan Hari Ini per Warung</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th>Dimsum</th>
                        <th>Cash</th>
                        <th>Modal</th>
                        <th>Omset</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($warungs as $warung)
                        @php
                            $tx = $hariIni->where('warung_id', $warung->id)->first();
                        @endphp
                        <tr>
                            <td class="font-medium">{{ $warung->nama_warung }}</td>
                            <td>{{ $tx ? number_format($tx->dimsum_terjual, 0, ',', '.') : '-' }}</td>
                            <td>{{ $tx ? 'Rp ' . number_format($tx->cash, 0, ',', '.') : '-' }}</td>
                            <td>{{ $tx ? 'Rp ' . number_format($tx->modal, 0, ',', '.') : '-' }}</td>
                            <td class="font-semibold text-emerald-600">{{ $tx ? 'Rp ' . number_format($tx->omset, 0, ',', '.') : '-' }}</td>
                            <td>
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
                        <td>{{ number_format($hariIni->sum('dimsum_terjual'), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($hariIni->sum('cash'), 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($hariIni->sum('modal'), 0, ',', '.') }}</td>
                        <td class="text-emerald-600">Rp {{ number_format($hariIni->sum('omset'), 0, ',', '.') }}</td>
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
            type: 'bar',
            data: {
                labels: chartData.map(d => d.tanggal),
                datasets: [
                    {
                        label: 'Omset',
                        data: chartData.map(d => d.omset),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Operasional',
                        data: chartData.map(d => d.operasional),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
