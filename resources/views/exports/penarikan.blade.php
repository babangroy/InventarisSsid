<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penarikan Barang</title>
    <style>
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 12px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            color: #000000;
        }
        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th { 
            background-color: #d7d5d5; 
            border: 1px solid #000000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        td { 
            border: 1px solid #000000; 
            padding: 5px;
        }
        .footer {
            margin-top: 5px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penarikan Barang</h1>
    </div>

    <div class="info">
        <div><strong>{{ $periode }}</strong></div>
        <div><strong>{{ $kondisi }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama/Tipe</th>
                <th>Merek</th>
                <th>Jenis</th>
                <th>Serial Number</th>
                <th>Asal Barang</th>
                <th>Alasan</th>
                <th>Kondisi</th>
                <th>Tanggal Ditarik</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left">{{ $record->barang->nama ?? '-' }}</td>
                <td class="text-left">{{ $record->barang->merek->nama ?? '-' }}</td>
                <td class="text-left">{{ $record->barang->jenis->nama ?? '-' }}</td>
                <td class="text-left">{{ $record->sn }}</td>
                <td class="text-left">{{ $record->asal }}</td>
                <td class="text-left">{{ $record->alasan }}</td>
                <td class="text-left">{{ $record->kondisi }}</td>
                <td class="text-center">
                    @if($record->tanggal_tarik)
                        {{ \Carbon\Carbon::parse($record->tanggal_tarik)->translatedFormat('l, d M Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ $exportDate }}
    </div>
</body>
</html>