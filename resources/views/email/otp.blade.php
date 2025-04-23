<!DOCTYPE html>
<html lang="id" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kode OTP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f7;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            transition: all 0.3s ease;
        }

        .header {
            background: linear-gradient(135deg, #1e90ff, #4b0082);
            padding: 25px;
            text-align: center;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 30px;
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
            text-align: center;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #1e90ff;
            margin: 20px 0;
            letter-spacing: 5px;
        }

        .footer {
            font-size: 14px;
            color: #888888;
            text-align: center;
            padding: 20px;
            background-color: #f4f4f7;
            border-top: 1px solid #dddddd;
        }

        @media screen and (max-width: 600px) {
            .email-container {
                width: 90% !important;
            }

            .header,
            .content {
                padding: 20px;
            }

            .content {
                font-size: 14px;
            }

            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>

<body>

<div class="email-container">
    <div class="header">
        Kode OTP Anda
    </div>
    <div class="content">
        <p>Gunakan kode OTP berikut untuk verifikasi Anda. Kode ini berlaku selama 10 menit:</p>
        <div class="otp-code">
            {{ $otp }}
        </div>
        <p>Jangan berikan kode ini kepada siapa pun. Jika Anda tidak meminta OTP, abaikan email ini.</p>
    </div>

    <div class="footer">
        &copy; 2025 Universitas Nurul Jadid
    </div>
</div>

</body>

</html>
