<!DOCTYPE html>
<html>
<head>
    <title>Laporan Profit Salon</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin-bottom: 5px; color: #d63384; }
        .info { margin-bottom: 20px; font-size: 14px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background-color: #f8f9fa; }
        .total-row { font-weight: bold; font-size: 18px; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #0d6efd; }
        .footer { margin-top: 50px; font-size: 12px; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Indah Sarisalon</h1>
        <p>Laporan Profit & Keuangan Keseluruhan</p>
    </div>

    <div class="info">
        <p><strong>Tanggal Cetak:</strong> {{ $date }}</p>
        <p><strong>Status:</strong> Laporan Keseluruhan (Tanpa Filter)</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Pemasukan</td>
                <td style="text-align: right;" class="text-success">Rp {{ number_format($pemasukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td style="text-align: right;" class="text-danger">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Profit Bersih (Keuntungan)</td>
                <td style="text-align: right;" class="text-primary">Rp {{ number_format($profit, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Terima kasih telah menggunakan sistem manajemen Indah Sarisalon.</p>
        <p>&copy; {{ date('Y') }} Indah Sarisalon. All rights reserved.</p>
    </div>
</body>
</html>
