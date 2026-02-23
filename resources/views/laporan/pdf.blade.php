<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ $periodeLabel }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #8B0000;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #8B0000;
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #8B0000;
            margin: 25px 0 5px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 5px;
            text-align: left;
        }
        th {
            background-color: #8B0000;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row {
            background-color: #f0f0f0 !important;
            font-weight: bold;
        }
        .positive { color: #059669; }
        .negative { color: #dc2626; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN</h1>
        <p>Periode: {{ $periodeLabel }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    <!-- Per-Product Table -->
    <div class="section-title">📦 Penjualan per Produk</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th class="text-right">Harga</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Omset</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produkData as $index => $produk)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $produk['nama'] }}</td>
                    <td class="text-right">Rp {{ number_format($produk['harga'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($produk['qty'], 0, ',', '.') }}</td>
                    <td class="text-right positive">Rp {{ number_format($produk['omset'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td class="text-center">{{ number_format($produkTotals['qty'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($produkTotals['omset'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Per-Warung Table -->
    <div class="section-title">🏪 Ringkasan per Warung</div>
    <table>
        <thead>
            <tr>
                <th>Warung</th>
                <th class="text-center">Hari</th>
                <th class="text-right">Omset</th>
                <th class="text-right">Operasional</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($warungData as $row)
                <tr style="{{ $row['is_tutup'] ? 'background-color: #fee2e2;' : '' }}">
                    <td>{{ $row['warung']->nama_warung }}</td>
                    <td class="text-center">
                        @if($row['is_tutup'])
                            <span style="color: #dc2626;">TUTUP ({{ $row['hari_tutup'] }} hari)</span>
                        @elseif($row['hari_kerja'] > 0)
                            {{ $row['hari_kerja'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($row['is_tutup'])
                            <span style="color: #dc2626;">Rp 0</span>
                        @else
                            Rp {{ number_format($row['omset'], 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-right negative">Rp {{ number_format($row['operasional'], 0, ',', '.') }}</td>
                    <td class="text-right {{ $row['net_profit'] >= 0 ? 'positive' : 'negative' }}">Rp {{ number_format($row['net_profit'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>TOTAL</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($warungTotals['omset'], 0, ',', '.') }}</td>
                <td class="text-right negative">Rp {{ number_format($warungTotals['operasional'], 0, ',', '.') }}</td>
                <td class="text-right {{ $warungTotals['net_profit'] >= 0 ? 'positive' : 'negative' }}">Rp {{ number_format($warungTotals['net_profit'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Manajemen Xipao &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
