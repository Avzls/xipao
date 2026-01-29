<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Libur Warung</title>
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
        .summary {
            margin-bottom: 20px;
        }
        .summary-box {
            display: inline-block;
            background: #fff7ed;
            border: 1px solid #fdba74;
            padding: 10px 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-box .number {
            font-size: 18px;
            font-weight: bold;
            color: #ea580c;
        }
        .summary-box .label {
            font-size: 10px;
            color: #9a3412;
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
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f0f0f0 !important;
            font-weight: bold;
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
        <h1>LAPORAN LIBUR WARUNG</h1>
        <p>Periode: {{ $periodeLabel }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    @if(count($summary) > 0)
    <div class="summary">
        <p><strong>Ringkasan per Warung:</strong></p>
        @foreach($summary as $s)
            <div class="summary-box">
                <div class="number">{{ $s->total_libur }}</div>
                <div class="label">{{ $s->warung->nama_warung ?? '-' }}</div>
            </div>
        @endforeach
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Hari</th>
                <th class="text-center">Warung</th>
                <th class="text-center">Alasan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($liburs as $index => $libur)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $libur->tanggal->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $libur->tanggal->translatedFormat('l') }}</td>
                    <td class="text-center">{{ $libur->warung->nama_warung }}</td>
                    <td class="text-center">{{ $libur->alasan ?? '-' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-center">TOTAL HARI LIBUR</td>
                <td class="text-center">{{ count($liburs) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Manajemen Xipao &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
