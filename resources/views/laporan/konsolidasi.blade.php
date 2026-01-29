@extends('layouts.app')

@section('page-title', 'Laporan')
@section('page-subtitle', 'Ringkasan performa semua warung')

@section('header-actions')
    <a href="{{ route('laporan.export.excel', ['tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir, 'warung_id' => $warungId]) }}" class="btn btn-success">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Excel
    </a>
    <a href="{{ route('laporan.export.pdf', ['tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir, 'warung_id' => $warungId]) }}" class="btn btn-danger">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        PDF
    </a>
@endsection


@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="card">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="form-label">Warung</label>
                <select name="warung_id" class="form-select">
                    <option value="">Semua Warung</option>
                    @foreach($allWarungs as $warung)
                        <option value="{{ $warung->id }}" {{ $warungId == $warung->id ? 'selected' : '' }}>{{ $warung->nama_warung }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="form-input">
            </div>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filter
            </button>
            @if($warungId || request('tanggal_awal') || request('tanggal_akhir'))
                <a href="{{ route('laporan.konsolidasi') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-label">Total Omset</p>
            <p class="stat-value text-lg text-emerald-600">Rp {{ number_format($totals['omset'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Biaya Operasional</p>
            <p class="stat-value text-lg text-red-600">Rp {{ number_format($totals['operasional'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Profit Bersih</p>
            <p class="stat-value text-lg {{ $totals['net_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($totals['net_profit'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Dimsum</p>
            <p class="stat-value text-lg text-purple-600">{{ number_format($totals['dimsum'], 0, ',', '.') }} pcs</p>
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
                        <th>Hari</th>
                        <th>Dimsum</th>
                        <th>Omset</th>
                        <th>Operasional</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr class="{{ $row['is_tutup'] ? 'bg-red-50' : '' }}">
                            <td class="font-medium">{{ $row['warung']->nama_warung }}</td>
                            <td>
                                @if($row['is_tutup'])
                                    <span class="badge bg-red-100 text-red-800">TUTUP ({{ $row['hari_tutup'] }} hari)</span>
                                @elseif($row['hari_tutup'] > 0)
                                    {{ $row['hari_kerja'] }} <span class="text-red-500 text-sm">(+{{ $row['hari_tutup'] }} tutup)</span>
                                @else
                                    {{ $row['hari_kerja'] }}
                                @endif
                            </td>
                            <td>
                                @if($row['is_tutup'])
                                    <span class="text-red-500">-</span>
                                @else
                                    {{ number_format($row['dimsum'], 0, ',', '.') }}
                                @endif
                            </td>
                            <td>
                                @if($row['is_tutup'])
                                    <span class="text-red-500">Rp 0</span>
                                @else
                                    Rp {{ number_format($row['omset'], 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="text-red-600">Rp {{ number_format($row['operasional'], 0, ',', '.') }}</td>
                            <td class="font-semibold {{ $row['net_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($row['net_profit'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-secondary-100 font-semibold">
                        <td>TOTAL</td>
                        <td>-</td>
                        <td>{{ number_format($totals['dimsum'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($totals['omset'], 0, ',', '.') }}</td>
                        <td class="text-red-600">Rp {{ number_format($totals['operasional'], 0, ',', '.') }}</td>
                        <td class="{{ $totals['net_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($totals['net_profit'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <h3 class="card-header">📈 Perbandingan per Warung</h3>
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
                datasets: [
                    {
                        label: 'Omset',
                        data: data.map(d => d.omset),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Operasional',
                        data: data.map(d => d.operasional),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Profit',
                        data: data.map(d => d.net_profit),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
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
