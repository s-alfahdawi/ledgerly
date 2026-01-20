<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Support\ReportPeriod;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getMonthlySummary(int $accountId, int $month, int $year, string $timezone = 'UTC'): array
    {
        $period = ReportPeriod::forMonth($month, $year, $timezone);

        $income = Transaction::forAccount($accountId)
            ->byType('income')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->sum('amount');

        $expense = Transaction::forAccount($accountId)
            ->byType('expense')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'net' => (float) $income - (float) $expense,
        ];
    }

    public function getCategoryBreakdown(int $accountId, string $startDate, string $endDate, string $timezone = 'UTC', ?string $transactionType = null): array
    {
        $period = ReportPeriod::fromDates($startDate, $endDate, $timezone);

        $query = Transaction::forAccount($accountId)
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()]);
        
        if ($transactionType) {
            $query->byType($transactionType);
        }

        return $query->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category?->name ?? 'Uncategorized',
                    'total' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    public function getWalletBreakdown(int $accountId, string $startDate, string $endDate, string $timezone = 'UTC', ?string $transactionType = null): array
    {
        $period = ReportPeriod::fromDates($startDate, $endDate, $timezone);

        $query = Transaction::forAccount($accountId)
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()]);
        
        if ($transactionType) {
            $query->byType($transactionType);
            // For income/expense filter, we need to adjust the balance calculation
            if ($transactionType === 'income') {
                $balanceCalc = 'SUM(amount)';
            } elseif ($transactionType === 'expense') {
                $balanceCalc = 'SUM(-amount)';
            } else {
                $balanceCalc = 'SUM(CASE WHEN type = "income" THEN amount ELSE -amount END)';
            }
        } else {
            $balanceCalc = 'SUM(CASE WHEN type = "income" THEN amount ELSE -amount END)';
        }

        return $query->select('wallet_id', DB::raw("{$balanceCalc} as balance"))
            ->groupBy('wallet_id')
            ->with('wallet')
            ->get()
            ->map(function ($item) {
                return [
                    'wallet' => $item->wallet->name,
                    'balance' => (float) $item->balance,
                ];
            })
            ->toArray();
    }

    public function getTopCategories(int $accountId, int $limit, string $startDate, string $endDate, string $timezone = 'UTC'): array
    {
        $period = ReportPeriod::fromDates($startDate, $endDate, $timezone);

        return Transaction::forAccount($accountId)
            ->byType('expense')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category?->name ?? 'Uncategorized',
                    'total' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    public function getWalletBalances(int $accountId): array
    {
        $wallets = Wallet::forAccount($accountId)->active()->get();

        if ($wallets->isEmpty()) {
            return [];
        }

        return $wallets->map(function ($wallet) use ($accountId) {
            $income = Transaction::forAccount($accountId)
                ->where('wallet_id', $wallet->id)
                ->byType('income')
                ->sum('amount');

            $expense = Transaction::forAccount($accountId)
                ->where('wallet_id', $wallet->id)
                ->byType('expense')
                ->sum('amount');

            return [
                'wallet' => $wallet->name,
                'balance' => (float) ($wallet->opening_balance + $income - $expense),
            ];
        })->toArray();
    }
}
