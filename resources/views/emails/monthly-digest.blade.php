<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Financial Digest</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">Monthly Digest</h1>
        <p style="color: rgba(255,255,255,0.85); margin: 5px 0 0;">{{ $monthLabel }}</p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <p>Hi {{ $user->name }},</p>
        <p>Here is your financial summary for <strong>{{ $account->name }}</strong> in <strong>{{ $monthLabel }}</strong>.</p>

        {{-- Summary Cards --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
            <tr>
                <td style="padding: 10px; text-align: center; background: #d4edda; border-radius: 8px;">
                    <small style="color: #155724;">INCOME</small><br>
                    <strong style="font-size: 20px; color: #155724;">{{ number_format($summary['income'], 2) }}</strong>
                    <br><small style="color: #155724;">{{ $account->currency_code }}</small>
                </td>
                <td width="10"></td>
                <td style="padding: 10px; text-align: center; background: #f8d7da; border-radius: 8px;">
                    <small style="color: #721c24;">EXPENSE</small><br>
                    <strong style="font-size: 20px; color: #721c24;">{{ number_format($summary['expense'], 2) }}</strong>
                    <br><small style="color: #721c24;">{{ $account->currency_code }}</small>
                </td>
                <td width="10"></td>
                <td style="padding: 10px; text-align: center; background: {{ $summary['net'] >= 0 ? '#cce5ff' : '#f8d7da' }}; border-radius: 8px;">
                    <small style="color: {{ $summary['net'] >= 0 ? '#004085' : '#721c24' }};">NET</small><br>
                    <strong style="font-size: 20px; color: {{ $summary['net'] >= 0 ? '#004085' : '#721c24' }};">{{ number_format($summary['net'], 2) }}</strong>
                    <br><small style="color: {{ $summary['net'] >= 0 ? '#004085' : '#721c24' }};">{{ $account->currency_code }}</small>
                </td>
            </tr>
        </table>

        {{-- Top Expense Categories --}}
        @if(!empty($topCategories))
        <h3 style="color: #555; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Top Expense Categories</h3>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
            @foreach($topCategories as $cat)
            <tr style="border-bottom: 1px solid #eee;">
                <td>{{ $cat['category'] }}</td>
                <td style="text-align: right; color: #dc3545; font-weight: bold;">
                    {{ number_format($cat['total'], 2) }} {{ $account->currency_code }}
                </td>
            </tr>
            @endforeach
        </table>
        @endif

        {{-- Wallet Balances --}}
        @if(!empty($walletBalances))
        <h3 style="color: #555; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 25px;">Current Wallet Balances</h3>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
            @foreach($walletBalances as $wallet)
            <tr style="border-bottom: 1px solid #eee;">
                <td>{{ $wallet['wallet'] }}</td>
                <td style="text-align: right; font-weight: bold; color: {{ $wallet['balance'] >= 0 ? '#28a745' : '#dc3545' }};">
                    {{ number_format($wallet['balance'], 2) }} {{ $account->currency_code }}
                </td>
            </tr>
            @endforeach
        </table>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('dashboard') }}"
               style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                View Dashboard
            </a>
        </div>

        <p style="font-size: 12px; color: #999; margin-top: 30px; text-align: center;">
            This is an automated monthly digest from {{ config('app.name', 'Ledgerly') }}.
        </p>
    </div>
</body>
</html>
