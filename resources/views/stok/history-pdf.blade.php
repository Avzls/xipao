<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan History Stock Masuk</title>
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
        <h1>LAPORAN HISTORY STOCK MASUK</h1>
        <p>Periode: {{ $periodeLabel }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Qty Masuk</th>
                <th class="text-center">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $index => $history)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $history->item->nama_item ?? '-' }}</td>
                    <td class="text-center">{{ $history->item->satuan ?? '-' }}</td>
                    <td class="text-center positive">+{{ number_format($history->qty_masuk, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $history->tanggal_masuk->format('d/m/Y') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td class="text-center positive">+{{ number_format($total, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Manajemen Xipao &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
