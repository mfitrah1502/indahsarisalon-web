<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan - Indah Sarisalon</title>
    <style>
        @page { margin: 100px 50px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #2c3e50; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 50px; position: relative; }
        .header h1 { margin: 0; color: #d63384; font-size: 28px; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #7f8c8d; font-size: 14px; text-transform: uppercase; }
        .divider { height: 2px; background: linear-gradient(to right, #ffffff, #d63384, #ffffff); margin: 20px 0; }
        
        .info-section { margin-bottom: 40px; }
        .info-box { background: #fdf2f7; padding: 15px; border-radius: 8px; border-left: 4px solid #d63384; }
        .info-box p { margin: 5px 0; font-size: 13px; }
        
        .table-container { margin-top: 30px; }
        .table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table th { background-color: #f8f9fa; color: #34495e; font-weight: bold; padding: 15px; text-align: left; border-bottom: 2px solid #dee2e6; text-transform: uppercase; font-size: 12px; }
        .table td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; }
        
        .summary-row { background-color: #fff; }
        .summary-row.total { background-color: #f8f9fa; font-weight: bold; }
        .summary-row.total td { border-top: 2px solid #34495e; font-size: 16px; color: #2c3e50; }
        
        .text-right { text-align: right; }
        .text-success { color: #27ae60; font-weight: bold; }
        .text-danger { color: #e74c3c; font-weight: bold; }
        .text-primary { color: #3498db; font-weight: bold; }
        
        .footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 50px; text-align: center; font-size: 10px; color: #bdc3c7; border-top: 1px solid #eee; padding-top: 10px; }
        .stamp { position: absolute; right: 0; top: 0; border: 3px solid #d63384; color: #d63384; padding: 10px; font-weight: bold; transform: rotate(-15deg); opacity: 0.2; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="stamp">OFFICIAL REPORT</div>
        <h1>Indah Sarisalon</h1>
        <p>Ringkasan Profit & Keuangan Keseluruhan</p>
        <div class="divider"></div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <p><strong>ID Laporan:</strong> #REP-{{ date('Ymd') }}-{{ rand(100, 999) }}</p>
            <p><strong>Tanggal Cetak:</strong> {{ $date }}</p>
            <p><strong>Cakupan Data:</strong> Seluruh Transaksi (Hingga Saat Ini)</p>
        </div>
    </div>

    <div class="table-container">
        <h3 style="font-size: 14px; margin-bottom: 10px; color: #34495e;">Rincian Transaksi Harian</h3>
        <table class="table" style="margin-bottom: 30px;">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori / Deskripsi</th>
                    <th>Tipe</th>
                    <th class="text-right">Jumlah (IDR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                <tr>
                    <td style="font-size: 12px;">{{ $item['date'] }}</td>
                    <td style="font-size: 12px;">{{ $item['description'] }}</td>
                    <td style="font-size: 12px;">{{ $item['type'] }}</td>
                    <td class="text-right {{ $item['class'] }}" style="font-size: 12px;">
                        {{ $item['type'] == 'Pengeluaran' ? '-' : '' }}Rp {{ number_format($item['amount'], 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3 style="font-size: 14px; margin-bottom: 10px; color: #34495e;">Ringkasan Keseluruhan</h3>
        <table class="table" style="border: 1px solid #dee2e6;">
            <thead>
                <tr>
                    <th style="border-bottom: 2px solid #dee2e6;">Deskripsi Laporan</th>
                    <th class="text-right" style="border-bottom: 2px solid #dee2e6;">Nilai Nominal (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border-bottom: 1px solid #eee;">Total Seluruh Pemasukan</td>
                    <td class="text-right text-success" style="border-bottom: 1px solid #eee;">Rp {{ number_format($pemasukan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #eee;">Total Seluruh Pengeluaran</td>
                    <td class="text-right text-danger" style="border-bottom: 1px solid #eee;">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <td style="font-weight: bold;">Profit Bersih (Net Income)</td>
                    <td class="text-right text-primary" style="font-weight: bold;">Rp {{ number_format($profit, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Indah Sarisalon Management System. Hak Cipta Dilindungi Undang-Undang.</p>
        <p>Cetak oleh Admin pada {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
