@extends('layouts.app')

@section('page-title', 'Laporan')
@section('page-subtitle', 'Ringkasan performa per produk & per warung')

@section('header-actions')
    <a href="{{ route('laporan.export.excel', ['tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir, 'warung_id' => $warungId, 'item_id' => $itemId]) }}" class="btn btn-success">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Excel
    </a>
    <a href="{{ route('laporan.export.pdf', ['tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir, 'warung_id' => $warungId, 'item_id' => $itemId]) }}" class="btn btn-danger">
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
                <label class="form-label">Produk</label>
                <select name="item_id" class="form-select">
                    <option value="">Semua Produk</option>
                    @foreach($allItems as $item)
                        <option value="{{ $item->id }}" {{ $itemId == $item->id ? 'selected' : '' }}>{{ $item->nama_item }}</option>
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
            @if($warungId || $itemId || request('tanggal_awal') || request('tanggal_akhir'))
                <a href="{{ route('laporan.konsolidasi') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="stat-label">Total Produk Terjual</p>
            <p class="stat-value text-lg text-purple-600">{{ number_format($produkTotals['qty'], 0, ',', '.') }} pcs</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Omset Produk</p>
            <p class="stat-value text-lg text-blue-600">Rp {{ number_format($produkTotals['omset'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Operasional</p>
            <p class="stat-value text-lg text-red-600">Rp {{ number_format($warungTotals['operasional'], 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Profit Bersih</p>
            <p class="stat-value text-lg {{ $warungTotals['net_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($warungTotals['net_profit'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Per-Product Table -->
    <div class="card">
        <h3 class="card-header">📦 Penjualan per Produk</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Harga Satuan</th>
                        <th>Qty Terjual</th>
                        <th>Omset</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produkData as $index => $produk)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-medium">{{ $produk['nama'] }}</td>
                            <td>Rp {{ number_format($produk['harga'], 0, ',', '.') }}</td>
                            <td>{{ number_format($produk['qty'], 0, ',', '.') }}</td>
                            <td class="font-semibold text-emerald-600">Rp {{ number_format($produk['omset'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-gray-400 py-4">Tidak ada data produk</td></tr>
                    @endforelse
                </tbody>
                @if($produkData->count() > 0)
                <tfoot>
                    <tr class="bg-secondary-100 font-semibold">
                        <td colspan="3">TOTAL</td>
                        <td>{{ number_format($produkTotals['qty'], 0, ',', '.') }}</td>
                        <td class="text-emerald-600">Rp {{ number_format($produkTotals['omset'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Per-Warung Summary -->
    <div class="card">
        <h3 class="card-header">🏪 Ringkasan per Warung</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Warung</th>
                        <th>Hari</th>
                        <th>Omset</th>
                        <th>Operasional</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($warungData as $row)
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
                        <td>Rp {{ number_format($warungTotals['omset'], 0, ',', '.') }}</td>
                        <td class="text-red-600">Rp {{ number_format($warungTotals['operasional'], 0, ',', '.') }}</td>
                        <td class="{{ $warungTotals['net_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($warungTotals['net_profit'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <h3 class="card-header">📈 Omset per Produk</h3>
        <canvas id="produkChart" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('produkChart').getContext('2d');
        const produkData = @json($produkData);
        
        const colors = [
            '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6',
            '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'
        ];
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: produkData.map(d => d.nama),
                datasets: [
                    {
                        label: 'Qty Terjual',
                        data: produkData.map(d => d.qty),
                        backgroundColor: colors.slice(0, produkData.length).map(c => c + 'cc'),
                        borderRadius: 4,
                    }
                ]
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
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
