<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 20px 0;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .note {
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>OTP Code</h2>
        <div class="otp-code">{{ $otp_code }}</div>
        <p class="note">Ini adalah Kode OTP untuk aplikasi Sales. Harap jangan membagikannya ke siapa pun.</p>
    </div>
</body>
</html>
