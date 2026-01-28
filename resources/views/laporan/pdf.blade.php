<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Konsolidasi - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
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
            font-size: 24px;
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
            padding: 10px 8px;
            text-align: left;
        }
        th {
            background-color: #8B0000;
            color: white;
            font-weight: bold;
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
        .summary-box {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .summary-box h3 {
            margin: 0 0 15px;
            color: #8B0000;
        }
        .summary-item {
            display: inline-block;
            width: 24%;
            text-align: center;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #8B0000;
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
        <h1>🍜 LAPORAN KONSOLIDASI MULTI WARUNG</h1>
        <p>Periode: {{ $namaBulan }} {{ $tahun }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Warung</th>
                <th class="text-center">Dimsum</th>
                <th class="text-right">Omset</th>
                <th class="text-right">Modal</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['warung'] }}</td>
                    <td class="text-center">{{ number_format($row['dimsum'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row['omset'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row['modal'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row['profit'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">TOTAL</td>
                <td class="text-center">{{ number_format($totals['dimsum'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['omset'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['modal'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totals['profit'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Manajemen Multi-Warung &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
