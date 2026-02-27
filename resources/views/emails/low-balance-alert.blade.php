<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Low Balance Alert</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">Low Balance Alert</h1>
        <p style="color: rgba(255,255,255,0.85); margin: 5px 0 0;">{{ $account->name }}</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <p>Hi {{ $user->name }},</p>

        <p>One or more of your income sources in <strong>{{ $account->name }}</strong> have reached a low balance:</p>

        <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background: #f8d7da;">
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #dc3545;">Source</th>
                    <th style="text-align: right; padding: 10px; border-bottom: 2px solid #dc3545;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowWallets as $wallet)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">{{ $wallet['wallet'] }}</td>
                    <td style="padding: 10px; text-align: right; font-weight: bold; color: #dc3545;">
                        {{ number_format($wallet['balance'], 2) }} {{ $currencyCode }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p>Consider adding funds or reviewing your recent transactions to ensure your accounts stay healthy.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('dashboard') }}"
               style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                View Dashboard
            </a>
        </div>

        <p style="font-size: 12px; color: #999; margin-top: 30px; text-align: center;">
            This is an automated alert from {{ config('app.name', 'Ledgerly') }}.
        </p>
    </div>
</body>
</html>
