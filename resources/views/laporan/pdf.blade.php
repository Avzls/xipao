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
            margin-bottom: 30px;
            border-bottom: 2px solid #8B0000;
            padding-bottom: 20px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 6px;
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f0f0f0 !important;
            font-weight: bold;
        }
        .positive {
            color: #059669;
        }
        .negative {
            color: #dc2626;
        }
        .footer {
            margin-top: 40px;
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

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Warung</th>
                <th class="text-center">Hari</th>
                <th class="text-center">Produk</th>
                <th class="text-right">Omset</th>
                <th class="text-right">Operasional</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                <tr style="{{ ($row['is_tutup'] ?? false) ? 'background-color: #fee2e2;' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['warung'] }}</td>
                    <td class="text-center">
                        @if($row['is_tutup'] ?? false)
                            <span style="color: #dc2626;">TUTUP ({{ $row['hari_tutup'] ?? 0 }} hari)</span>
                        @elseif(($row['hari_kerja'] ?? 0) > 0)
                            {{ $row['hari_kerja'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: left; font-size: 10px;">
                        @if($row['is_tutup'] ?? false)
                            <span style="color: #dc2626;">-</span>
                        @else
                            @forelse($row['produk_detail'] as $nama => $qty)
                                {{ $nama }}: {{ $qty }}<br>
                            @empty
                                -
                            @endforelse
                        @endif
                    </td>
                    <td class="text-right">
                        @if($row['is_tutup'] ?? false)
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
                <td colspan="3">TOTAL</td>
                <td class="text-center">{{ number_format($totals['produk_qty'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['omset'], 0, ',', '.') }}</td>
                <td class="text-right negative">Rp {{ number_format($totals['operasional'], 0, ',', '.') }}</td>
                <td class="text-right {{ $totals['net_profit'] >= 0 ? 'positive' : 'negative' }}">Rp {{ number_format($totals['net_profit'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Manajemen Xipao &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
