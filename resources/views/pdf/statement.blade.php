<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statement - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Statement - {{ $account->name }}</h1>
    <p>Period: {{ $startDate }} to {{ $endDate }}</p>
    
    <h2>Summary</h2>
    <table>
        <tr>
            <th>Income</th>
            <th>Expense</th>
            <th>Net</th>
        </tr>
        <tr>
            <td>{{ number_format($summary['income'], 2) }}</td>
            <td>{{ number_format($summary['expense'], 2) }}</td>
            <td>{{ number_format($summary['net'], 2) }}</td>
        </tr>
    </table>
    
    <h2>Category Breakdown</h2>
    <table>
        <tr>
            <th>Category</th>
            <th>Total</th>
        </tr>
        @foreach($categoryBreakdown as $item)
            <tr>
                <td>{{ $item['category'] }}</td>
                <td>{{ number_format($item['total'], 2) }}</td>
            </tr>
        @endforeach
    </table>
    
    <h2>Transactions</h2>
    <table>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Wallet</th>
            <th>Category</th>
        </tr>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->occurred_at->format('M d, Y') }}</td>
                <td>{{ ucfirst($transaction->type) }}</td>
                <td>{{ number_format($transaction->amount, 2) }}</td>
                <td>{{ $transaction->wallet->name }}</td>
                <td>{{ $transaction->category->name ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
