<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Indah Sari Salon - OTP Anda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #fbe6eb;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #ea8290;
            color: white;
            text-align: center;
            padding: 30px;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 30px;
            text-align: center;
        }

        .otp-code {
            display: inline-block;
            font-size: 32px;
            font-weight: bold;
            background-color: #fce4e6;
            color: #ea8290;
            padding: 15px 25px;
            border-radius: 8px;
            margin: 20px 0;
            letter-spacing: 5px;
        }

        .note {
            font-size: 14px;
            color: #666;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #999;
            background-color: #fbe6eb;
        }

        @media only screen and (max-width: 600px) {
            .container {
                margin: 20px;
            }

            .otp-code {
                font-size: 28px;
                padding: 12px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            Indah Sari Salon
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Ini adalah kode OTP Anda untuk reset password:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p class="note"><em>Jangan bagikan kode ini kepada siapapun. Kode berlaku 10 menit.</em></p>
            <p>Terima kasih telah menggunakan layanan kami!</p>
        </div>
        <div class="footer">
            &copy; 2026 Indah Sari Salon. Semua hak cipta dilindungi.
        </div>
    </div>
</body>

</html>