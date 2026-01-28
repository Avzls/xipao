@extends('layouts.app')

@section('page-title', 'Laporan Konsolidasi')
@section('page-subtitle', 'Ringkasan performa semua warung')

@section('header-actions')
    <a href="{{ route('laporan.export.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export Excel
    </a>
    <a href="{{ route('laporan.export.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-danger">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        Export PDF
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-label">Total Omset</p>
            <p class="stat-value text-emerald-600">Rp {{ number_format($totals['omset'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Modal</p>
            <p class="stat-value text-amber-600">Rp {{ number_format($totals['modal'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Profit</p>
            <p class="stat-value text-blue-600">Rp {{ number_format($totals['profit'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Dimsum</p>
            <p class="stat-value text-purple-600">{{ number_format($totals['dimsum'], 0, ',', '.') }} pcs</p>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <h3 class="card-header">📊 Detail per Warung</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th class="text-center">Hari Kerja</th>
                        <th class="text-center">Dimsum</th>
                        <th class="text-right">Omset</th>
                        <th class="text-right">Modal</th>
                        <th class="text-right">Profit</th>
                        <th class="text-center">Kontribusi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        @php
                            $kontribusi = $totals['omset'] > 0 ? ($row['omset'] / $totals['omset']) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="font-medium">{{ $row['warung']->nama_warung }}</td>
                            <td class="text-center">{{ $row['hari_kerja'] }} hari</td>
                            <td class="text-center">{{ number_format($row['dimsum'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($row['omset'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($row['modal'], 0, ',', '.') }}</td>
                            <td class="text-right font-semibold text-emerald-600">Rp {{ number_format($row['profit'], 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ number_format($kontribusi, 1) }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-secondary-100 font-semibold">
                        <td>TOTAL</td>
                        <td class="text-center">-</td>
                        <td class="text-center">{{ number_format($totals['dimsum'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($totals['omset'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($totals['modal'], 0, ',', '.') }}</td>
                        <td class="text-right text-emerald-600">Rp {{ number_format($totals['profit'], 0, ',', '.') }}</td>
                        <td class="text-center">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <h3 class="card-header">📈 Perbandingan Omset Warung</h3>
        <canvas id="barChart" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('barChart').getContext('2d');
        const data = @json($data);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.warung.nama_warung),
                datasets: [{
                    label: 'Omset',
                    data: data.map(d => d.omset),
                    backgroundColor: '#8B0000',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
