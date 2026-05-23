<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #BOOK-{{ $booking->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono&display=swap');
        
        body {
            font-family: 'Space Mono', 'Outfit', monospace, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f7f7f7;
        }

        .receipt-container {
            width: 80mm;
            background-color: #fff;
            padding: 20px;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #EA8290;
            margin: 0 0 5px 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }

        .divider {
            border-top: 1px dashed #bbb;
            margin: 15px 0;
        }

        .info-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-table td.label {
            color: #666;
            width: 45%;
        }

        .info-table td.value {
            text-align: right;
            font-weight: 600;
            color: #222;
        }

        .items-table {
            width: 100%;
            font-size: 11px;
            border-collapse: collapse;
        }

        .items-table th {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            text-align: left;
            border-bottom: 1px dashed #bbb;
            padding-bottom: 5px;
            color: #444;
        }

        .items-table td {
            padding: 8px 0;
            vertical-align: top;
        }

        .item-name {
            font-weight: 600;
            color: #222;
        }

        .item-meta {
            font-size: 10px;
            color: #888;
            margin-top: 2px;
        }

        .item-price {
            text-align: right;
            font-weight: 600;
            color: #222;
        }

        .totals-table {
            width: 100%;
            font-size: 11px;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 3px 0;
        }

        .totals-table td.label {
            color: #555;
        }

        .totals-table td.value {
            text-align: right;
            font-weight: 600;
        }

        .totals-table tr.grand-total td {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            padding-top: 10px;
            border-top: 1px dashed #bbb;
        }

        .totals-table tr.grand-total td.value {
            color: #EA8290;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 10px;
            color: #888;
        }

        .footer p {
            margin: 3px 0;
        }

        .footer .thank-you {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: #EA8290;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .no-print-btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .print-btn {
            background-color: #EA8290;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(234, 130, 144, 0.2);
            transition: all 0.2s ease;
        }

        .print-btn:hover {
            background-color: #d96f7c;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
                margin: 0;
            }

            .receipt-container {
                width: 100%;
                max-width: 80mm;
                box-shadow: none;
                padding: 10px;
                border-radius: 0;
                margin: 0;
            }

            .no-print-btn-container {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <div class="header">
            <h2>Indah Sari</h2>
            <p>Salon & Beauty Care</p>
            <p style="font-size: 9px; line-height: 1.2;">Jl. Raya Indah Sari No. 123, Indonesia</p>
            <p style="font-size: 9px;">Telp: 0812-3456-7890</p>
        </div>

        <div class="divider"></div>

        <table class="info-table">
            <tr>
                <td class="label">No. Transaksi</td>
                <td class="value">#BOOK-{{ $booking->id }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td class="value">{{ \Carbon\Carbon::parse($booking->reservation_datetime)->format('d/m/Y H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Pelanggan</td>
                <td class="value">{{ $booking->customer_name }}</td>
            </tr>
            <tr>
                <td class="label">Telepon</td>
                <td class="value">{{ $booking->customer_phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Metode Bayar</td>
                <td class="value">{{ strtoupper($booking->payment_method ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">Status Bayar</td>
                <td class="value" style="color: {{ $booking->payment_status === 'paid' ? '#2e7d32' : '#c62828' }}">
                    {{ $booking->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                </td>
            </tr>
            @if($booking->cashier)
            <tr>
                <td class="label">Kasir</td>
                <td class="value">{{ $booking->cashier->name }}</td>
            </tr>
            @endif
        </table>

        <div class="divider"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Layanan</th>
                    <th style="width: 30%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->details as $detail)
                <tr>
                    <td>
                        <div class="item-name">{{ $detail->treatmentDetail ? $detail->treatmentDetail->name : 'Layanan' }}</div>
                        @if($detail->stylist)
                        <div class="item-meta">Stylist: {{ $detail->stylist->name }}</div>
                        @elseif($booking->stylist)
                        <div class="item-meta">Stylist: {{ $booking->stylist->name }}</div>
                        @endif
                    </td>
                    <td class="item-price">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <table class="totals-table">
            <tr>
                <td class="label">Subtotal</td>
                <td class="value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
            </tr>
            <tr class="grand-total">
                <td class="label">TOTAL</td>
                <td class="value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
            </tr>
            @if(isset($nominal) && $nominal !== null)
            @php
                $nominalVal = (int) $nominal;
                $kembalian = $nominalVal - $booking->total_price;
            @endphp
            <tr>
                <td class="label" style="padding-top: 8px;">Bayar</td>
                <td class="value" style="padding-top: 8px;">Rp {{ number_format($nominalVal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Kembalian</td>
                <td class="value">Rp {{ number_format(max(0, $kembalian), 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>

        <div class="footer">
            <p class="thank-you">Terima Kasih</p>
            <p>Atas kunjungan Anda ke Indah Sari Salon</p>
            <p>Layanan Terbaik untuk Cantik Maksimal Anda</p>
        </div>
    </div>

    <div class="no-print-btn-container">
        <button class="print-btn" onclick="window.print()">Cetak Struk</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
