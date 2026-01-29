<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Transaksi {{ $periodeLabel }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
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
            font-size: 20px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
            font-size: 9px;
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
        .tutup-row {
            background-color: #fee2e2 !important;
        }
        .status-buka {
            color: #059669;
            font-weight: bold;
        }
        .status-tutup {
            color: #dc2626;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL TRANSAKSI</h1>
        <p>Periode: {{ $periodeLabel }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Warung</th>
                <th class="text-center">Status</th>
                <th class="text-center">Dimsum</th>
                <th class="text-right">Modal</th>
                <th class="text-right">Cash</th>
                <th class="text-right">Omset</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $index => $tx)
                <tr class="{{ $tx->status === 'tutup' ? 'tutup-row' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</td>
                    <td>{{ $tx->warung->nama_warung ?? '-' }}</td>
                    <td class="text-center {{ $tx->status === 'tutup' ? 'status-tutup' : 'status-buka' }}">
                        {{ strtoupper($tx->status) }}
                    </td>
                    <td class="text-center">
                        @if($tx->status === 'tutup')
                            -
                        @else
                            {{ number_format($tx->dimsum_terjual, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($tx->modal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($tx->cash, 0, ',', '.') }}</td>
                    <td class="text-right {{ $tx->omset >= 0 ? 'positive' : 'negative' }}">
                        Rp {{ number_format($tx->omset, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data transaksi</td>
                </tr>
            @endforelse
            @if($transaksis->count() > 0)
            <tr class="total-row">
                <td colspan="4">TOTAL</td>
                <td class="text-center">{{ number_format($totals['dimsum'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['modal'], 0, ',', '.') }}</td>
                <td class="text-right">-</td>
                <td class="text-right {{ $totals['omset'] >= 0 ? 'positive' : 'negative' }}">
                    Rp {{ number_format($totals['omset'], 0, ',', '.') }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Manajemen Xipao &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
