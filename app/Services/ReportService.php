<?php

namespace App\Services;

use App\Models\Budget;
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
                $balanceCalc = "SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END)";
            }
        } else {
            $balanceCalc = "SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END)";
        }

        return $query->select('wallet_id', DB::raw("{$balanceCalc} as balance"))
            ->groupBy('wallet_id')
            ->with('wallet')
            ->get()
            ->map(function ($item) {
                return [
                    'wallet' => $item->wallet?->name ?? 'Unknown Wallet',
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
                    'category' => $item->category?->name  ?? 'Uncategorized',
                    'color'    => $item->category?->color ?? '#f06548',
                    'total'    => (float) $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get all-time income, expense, and current cash totals.
     */
    public function getAllTimeSummary(int $accountId): array
    {
        $income = Transaction::forAccount($accountId)
            ->byType('income')
            ->sum('amount');

        $expense = Transaction::forAccount($accountId)
            ->byType('expense')
            ->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'current_cash' => (float) $income - (float) $expense,
        ];
    }

    /**
     * Get all categories of a given type with their total transaction amounts.
     */
    public function getCategoryTotals(int $accountId, string $type): array
    {
        return Transaction::forAccount($accountId)
            ->byType($type)
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category'          => $item->category?->name  ?? 'Uncategorized',
                    'color'             => $item->category?->color ?? '#405189',
                    'total'             => (float) $item->total,
                    'transaction_count' => (int)   $item->transaction_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get all wallets with their total income transaction amounts.
     */
    public function getWalletIncomeTotals(int $accountId): array
    {
        return Transaction::forAccount($accountId)
            ->byType('income')
            ->select('wallet_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('wallet_id')
            ->orderByDesc('total')
            ->with('wallet')
            ->get()
            ->map(function ($item) {
                return [
                    'wallet' => $item->wallet?->name ?? 'Unknown Source',
                    'total' => (float) $item->total,
                    'transaction_count' => (int) $item->transaction_count,
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

        // Single query to get net amounts for all wallets
        $walletIds = $wallets->pluck('id');
        $totals = Transaction::forAccount($accountId)
            ->whereIn('wallet_id', $walletIds)
            ->whereIn('type', ['income', 'expense'])
            ->select(
                'wallet_id',
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense")
            )
            ->groupBy('wallet_id')
            ->get()
            ->keyBy('wallet_id');

        return $wallets->map(function ($wallet) use ($totals) {
            $walletTotals = $totals->get($wallet->id);
            $income = $walletTotals ? (float) $walletTotals->total_income : 0;
            $expense = $walletTotals ? (float) $walletTotals->total_expense : 0;

            return [
                'wallet' => $wallet->name,
                'balance' => (float) ($wallet->opening_balance + $income - $expense),
            ];
        })->toArray();
    }

    /**
     * Get monthly income/expense/net for the last N months in a single query.
     * Replaces calling getMonthlySummary() in a loop (N*2 queries -> 1 query).
     */
    public function getMonthlyTrend(int $accountId, int $months, string $timezone = 'UTC'): array
    {
        $now = now($timezone);
        $startDate = $now->copy()->subMonths($months - 1)->startOfMonth();
        $period = ReportPeriod::fromDates(
            $startDate->toDateString(),
            $now->copy()->endOfMonth()->toDateString(),
            $timezone
        );

        // Use database-agnostic month grouping
        $driver = DB::getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', occurred_at)"
            : "DATE_FORMAT(occurred_at, '%Y-%m')";

        $rows = Transaction::forAccount($accountId)
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->select(
                DB::raw("{$monthExpr} as month_key"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            )
            ->groupBy('month_key')
            ->get()
            ->keyBy('month_key');

        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $key = $date->format('Y-m');
            $income = $rows->has($key) ? (float) $rows->get($key)->income : 0;
            $expense = $rows->has($key) ? (float) $rows->get($key)->expense : 0;

            $result[] = [
                'month' => $date->format('M Y'),
                'income' => $income,
                'expense' => $expense,
                'net' => $income - $expense,
            ];
        }

        return $result;
    }

    /**
     * Get year-to-date income, expense, and net for the given year.
     */
    public function getYearToDateSummary(int $accountId, int $year, string $timezone = 'UTC'): array
    {
        $now = now($timezone);
        $period = ReportPeriod::fromDates(
            $now->copy()->startOfYear()->toDateString(),
            $now->copy()->toDateString(),
            $timezone
        );

        $income = Transaction::forAccount($accountId)
            ->byType('income')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->sum('amount');

        $expense = Transaction::forAccount($accountId)
            ->byType('expense')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->sum('amount');

        return [
            'income'  => (float) $income,
            'expense' => (float) $expense,
            'net'     => (float) $income - (float) $expense,
        ];
    }

    /**
     * Get average monthly income and expense across all calendar months with data.
     * Ignores empty months to avoid deflating averages.
     */
    public function getAverageMonthlySummary(int $accountId, string $timezone = 'UTC'): array
    {
        $driver = DB::getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', occurred_at)"
            : "DATE_FORMAT(occurred_at, '%Y-%m')";

        $rows = Transaction::forAccount($accountId)
            ->whereIn('type', ['income', 'expense'])
            ->select(
                DB::raw("{$monthExpr} as month_key"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            )
            ->groupBy('month_key')
            ->get();

        $count = $rows->count();
        if ($count === 0) {
            return ['income' => 0.0, 'expense' => 0.0];
        }

        $totalIncome  = $rows->sum(fn ($r) => (float) $r->income);
        $totalExpense = $rows->sum(fn ($r) => (float) $r->expense);

        return [
            'income'  => round($totalIncome / $count, 2),
            'expense' => round($totalExpense / $count, 2),
        ];
    }

    /**
     * Get top N income categories by amount in a given date range.
     */
    public function getTopIncomeCategories(int $accountId, int $limit, string $startDate, string $endDate, string $timezone = 'UTC'): array
    {
        $period = ReportPeriod::fromDates($startDate, $endDate, $timezone);

        return Transaction::forAccount($accountId)
            ->byType('income')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category?->name  ?? 'Uncategorized',
                    'color'    => $item->category?->color ?? '#0ab39c',
                    'total'    => (float) $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get top N income sources (wallets) by amount in a given date range.
     * Wallets are the natural income dimension — more reliable than categories
     * since income transactions frequently have no category assigned.
     */
    public function getTopIncomeWallets(int $accountId, int $limit, string $startDate, string $endDate, string $timezone = 'UTC'): array
    {
        $period = ReportPeriod::fromDates($startDate, $endDate, $timezone);

        return Transaction::forAccount($accountId)
            ->byType('income')
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->select('wallet_id', DB::raw('SUM(amount) as total'))
            ->groupBy('wallet_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('wallet')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->wallet?->name ?? 'Unknown Source',
                    'total' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get daily income and expense for every calendar day in the current month.
     * Fills zeros for days with no transactions.
     */
    public function getDailyTrendCurrentMonth(int $accountId, string $timezone = 'UTC'): array
    {
        $now    = now($timezone);
        $period = ReportPeriod::forMonth($now->month, $now->year, $timezone);

        $driver  = DB::getDriverName();
        $dayExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', occurred_at)"
            : "DATE_FORMAT(occurred_at, '%Y-%m-%d')";

        $rows = Transaction::forAccount($accountId)
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('occurred_at', [$period->startDateString(), $period->endDateString()])
            ->select(
                DB::raw("{$dayExpr} as day_key"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            )
            ->groupBy('day_key')
            ->get()
            ->keyBy('day_key');

        $result    = [];
        $daysInMonth = $now->copy()->endOfMonth()->day;

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date    = $now->copy()->setDay($d);
            $key     = $date->format('Y-m-d');
            $income  = $rows->has($key) ? (float) $rows->get($key)->income  : 0.0;
            $expense = $rows->has($key) ? (float) $rows->get($key)->expense : 0.0;

            $result[] = [
                'day'     => $date->format('M d'),
                'date'    => $key,
                'income'  => $income,
                'expense' => $expense,
            ];
        }

        return $result;
    }

    /**
     * Find the best (highest net) and worst (lowest net) months from a trend array.
     * Accepts the result of getMonthlyTrend() — no extra DB query needed.
     */
    public function getBestAndWorstMonths(array $monthlyTrend): array
    {
        // Filter to months that have actual data
        $active = array_values(array_filter(
            $monthlyTrend,
            fn ($m) => $m['income'] > 0 || $m['expense'] > 0
        ));

        if (empty($active)) {
            return ['best' => null, 'worst' => null];
        }

        usort($active, fn ($a, $b) => $b['net'] <=> $a['net']);

        return [
            'best'  => $active[0],
            'worst' => $active[count($active) - 1],
        ];
    }

    /**
     * Get total count of income and expense transactions for an account (all time).
     */
    public function getAllTimeTransactionCounts(int $accountId): array
    {
        $rows = Transaction::forAccount($accountId)
            ->whereIn('type', ['income', 'expense'])
            ->select('type', DB::raw('COUNT(*) as cnt'))
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        return [
            'income_count'  => (int) ($rows->get('income')?->cnt  ?? 0),
            'expense_count' => (int) ($rows->get('expense')?->cnt ?? 0),
        ];
    }

    /**
     * Get the single largest income and expense transaction of all time.
     */
    public function getLargestTransactions(int $accountId): array
    {
        $largestIncome = Transaction::forAccount($accountId)
            ->byType('income')
            ->with(['category', 'wallet'])
            ->orderByDesc('amount')
            ->first();

        $largestExpense = Transaction::forAccount($accountId)
            ->byType('expense')
            ->with(['category', 'wallet'])
            ->orderByDesc('amount')
            ->first();

        return [
            'largest_income' => $largestIncome ? [
                'amount'      => (float) $largestIncome->amount,
                'category'    => $largestIncome->category?->name ?? 'Uncategorized',
                'wallet'      => $largestIncome->wallet?->name   ?? 'Unknown',
                'occurred_at' => $largestIncome->occurred_at->format('M d, Y'),
            ] : null,
            'largest_expense' => $largestExpense ? [
                'amount'      => (float) $largestExpense->amount,
                'category'    => $largestExpense->category?->name ?? 'Uncategorized',
                'wallet'      => $largestExpense->wallet?->name   ?? 'Unknown',
                'occurred_at' => $largestExpense->occurred_at->format('M d, Y'),
            ] : null,
        ];
    }

    /**
     * Get budget vs actual spending for active budgets in the current period.
     * Returns each budget with its category, limit, spent amount, and percentage.
     */
    public function getBudgetProgress(int $accountId, string $timezone = 'UTC'): array
    {
        $budgets = Budget::forAccount($accountId)
            ->active()
            ->with('category')
            ->get();

        if ($budgets->isEmpty()) {
            return [];
        }

        $now = now($timezone);

        // Get spending per category for the current period
        $categoryIds = $budgets->pluck('category_id')->filter()->unique();

        $periodMap = [];
        foreach ($budgets as $budget) {
            $period = match ($budget->period) {
                'weekly' => ReportPeriod::fromDates(
                    $now->copy()->startOfWeek()->toDateString(),
                    $now->copy()->endOfWeek()->toDateString(),
                    $timezone
                ),
                'yearly' => ReportPeriod::fromDates(
                    $now->copy()->startOfYear()->toDateString(),
                    $now->copy()->endOfYear()->toDateString(),
                    $timezone
                ),
                default => ReportPeriod::forMonth($now->month, $now->year, $timezone),
            };
            $periodMap[$budget->id] = $period;
        }

        // For monthly budgets (most common), batch the query
        $monthlyPeriod = ReportPeriod::forMonth($now->month, $now->year, $timezone);
        $spending = Transaction::forAccount($accountId)
            ->byType('expense')
            ->whereIn('category_id', $categoryIds)
            ->whereBetween('occurred_at', [$monthlyPeriod->startDateString(), $monthlyPeriod->endDateString()])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');

        return $budgets->map(function ($budget) use ($spending) {
            $spent = 0;
            if ($budget->category_id && $spending->has($budget->category_id)) {
                $spent = (float) $spending->get($budget->category_id)->total;
            }

            $limit = (float) $budget->amount;
            $percentage = $limit > 0 ? min(round(($spent / $limit) * 100, 1), 100) : 0;
            $remaining = max($limit - $spent, 0);

            return [
                'id' => $budget->id,
                'category' => $budget->category?->name ?? 'Overall',
                'category_color' => $budget->category?->color ?? '#405189',
                'period' => $budget->period,
                'limit' => $limit,
                'spent' => $spent,
                'remaining' => $remaining,
                'percentage' => $percentage,
                'over_budget' => $spent > $limit,
            ];
        })->toArray();
    }
}
