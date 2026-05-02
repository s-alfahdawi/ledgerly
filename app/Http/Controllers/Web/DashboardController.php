<?php

namespace App\Http\Controllers\Web;

use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\AccountContext;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private AccountContext $accountContext,
        private ReportService $reportService
    ) {
    }

    protected function createDefaultAccount($user): ?Account
    {
        return DB::transaction(function () use ($user) {
            $account = Account::create([
                'name' => $user->name . "'s Account",
                'currency_code' => Currency::IQD->value,
                'timezone' => 'Asia/Baghdad',
                'owner_user_id' => $user->id,
            ]);

            $account->users()->attach($user->id, [
                'role' => 'owner',
                'joined_at' => now(),
            ]);

            return $account;
        });
    }

    public function index()
    {
        $user = auth()->user();

        $account = $this->accountContext->resolve();

        if (!$account && $user) {
            $account = $this->createDefaultAccount($user);
            if ($account) {
                $this->accountContext->set($account->id);
            }
        }

        if (!$account) {
            return view('dashboard.no-account', [
                'user' => $user,
            ]);
        }

        $accountId = $account->id;
        $timezone = $account->timezone ?? 'UTC';
        $now = now($timezone);
        $cacheKey = "dashboard:{$accountId}:" . $now->format('Y-m-d-H');

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($accountId, $timezone, $now) {
            $summary = $this->reportService->getMonthlySummary(
                $accountId,
                $now->month,
                $now->year,
                $timezone
            );

            // Last month for trend comparison
            $lastMonth = $now->copy()->subMonth();
            $lastMonthSummary = $this->reportService->getMonthlySummary(
                $accountId,
                $lastMonth->month,
                $lastMonth->year,
                $timezone
            );

            $allTimeSummary = $this->reportService->getAllTimeSummary($accountId);

            $topCategories = $this->reportService->getTopCategories(
                $accountId,
                5,
                $now->copy()->startOfMonth()->toDateString(),
                $now->copy()->endOfMonth()->toDateString(),
                $timezone
            );

            $walletBalances = $this->reportService->getWalletBalances($accountId);
            $totalBalance = array_sum(array_column($walletBalances, 'balance'));
            $walletStats = $this->reportService->getWalletStats($accountId);

            $incomeWalletTotals = $this->reportService->getWalletIncomeTotals($accountId);
            $expenseCategoryTotals = $this->reportService->getCategoryTotals($accountId, 'expense');

            $last12Months = $this->reportService->getMonthlyTrend($accountId, 12, $timezone);

            // Extended analytics
            $ytdSummary          = $this->reportService->getYearToDateSummary($accountId, $now->year, $timezone);
            $avgMonthlySummary   = $this->reportService->getAverageMonthlySummary($accountId, $timezone);
            $topIncomeCategories = $this->reportService->getTopIncomeCategories(
                $accountId,
                5,
                $now->copy()->startOfMonth()->toDateString(),
                $now->copy()->endOfMonth()->toDateString(),
                $timezone
            );
            $topIncomeWallets = $this->reportService->getTopIncomeWallets(
                $accountId,
                5,
                $now->copy()->startOfMonth()->toDateString(),
                $now->copy()->endOfMonth()->toDateString(),
                $timezone
            );
            $dailyTrend      = $this->reportService->getDailyTrendCurrentMonth($accountId, $timezone);
            $bestWorstMonths = $this->reportService->getBestAndWorstMonths($last12Months);
            $txCounts        = $this->reportService->getAllTimeTransactionCounts($accountId);
            $largestTxns     = $this->reportService->getLargestTransactions($accountId);

            // Budget progress
            $budgetProgress = $this->reportService->getBudgetProgress($accountId, $timezone);

            // Recent transactions
            $recentTransactions = \App\Models\Transaction::forAccount($accountId)
                ->with(['wallet', 'category'])
                ->orderByDesc('occurred_at')
                ->limit(5)
                ->get();

            // Transaction counts for comparison
            $monthStart = $now->copy()->startOfMonth()->toDateString();
            $monthEnd = $now->copy()->endOfMonth()->toDateString();
            $transactionCount = \App\Models\Transaction::forAccount($accountId)
                ->whereBetween('occurred_at', [$monthStart, $monthEnd])
                ->count();
            $lastMonthTxCount = \App\Models\Transaction::forAccount($accountId)
                ->whereBetween('occurred_at', [
                    $lastMonth->copy()->startOfMonth()->toDateString(),
                    $lastMonth->copy()->endOfMonth()->toDateString(),
                ])
                ->count();

            return compact(
                'summary', 'lastMonthSummary', 'allTimeSummary', 'topCategories',
                'walletBalances', 'totalBalance', 'walletStats', 'last12Months',
                'incomeWalletTotals', 'expenseCategoryTotals',
                'budgetProgress', 'recentTransactions',
                'transactionCount', 'lastMonthTxCount',
                'ytdSummary', 'avgMonthlySummary', 'topIncomeCategories', 'topIncomeWallets',
                'dailyTrend', 'bestWorstMonths', 'txCounts', 'largestTxns'
            );
        });

        return view('dashboard.index', array_merge($data, [
            'account' => $account,
            'monthlyData' => $data['last12Months'],
        ]));
    }

    /**
     * Reconcile a wallet's balance to a user-supplied "actual cash" amount.
     *
     * Creates an income or expense adjustment transaction equal to the
     * difference between actual and current system balance:
     *   - actual > system  → income leg (positive correction)
     *   - actual < system  → expense leg (negative correction)
     *   - actual == system → no-op
     */
    public function cashMatch(Request $request): RedirectResponse
    {
        $account = $this->accountContext->resolve();
        if (!$account) {
            abort(403, 'No account selected.');
        }

        $validated = $request->validate([
            'wallet_id'   => ['required', 'integer'],
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'note'        => ['nullable', 'string', 'max:500'],
        ]);

        $wallet = Wallet::forAccount($account->id)
            ->active()
            ->where('id', $validated['wallet_id'])
            ->first();

        if (!$wallet) {
            return back()->with('error', 'Selected wallet was not found.');
        }

        $totals = Transaction::forAccount($account->id)
            ->where('wallet_id', $wallet->id)
            ->whereIn('type', ['income', 'expense'])
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income")
            ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense")
            ->first();

        $income       = $totals ? (float) $totals->total_income  : 0.0;
        $expense      = $totals ? (float) $totals->total_expense : 0.0;
        $current      = (float) $wallet->opening_balance + $income - $expense;
        $actual       = (float) $validated['actual_cash'];
        $diff         = round($actual - $current, 2);

        if (abs($diff) < 0.01) {
            return back()->with('info', "{$wallet->name} is already balanced. No adjustment created.");
        }

        $type   = $diff > 0 ? 'income' : 'expense';
        $amount = abs($diff);
        $defaultNote = sprintf(
            'Cash adjustment — set %s balance to %s (was %s)',
            $wallet->name,
            number_format($actual, 2),
            number_format($current, 2)
        );

        DB::transaction(function () use ($account, $wallet, $type, $amount, $validated, $defaultNote, $request) {
            Transaction::create([
                'account_id'  => $account->id,
                'wallet_id'   => $wallet->id,
                'category_id' => null,
                'type'        => $type,
                'amount'      => $amount,
                'occurred_at' => now($account->timezone ?? 'UTC'),
                'note'        => $validated['note'] ?? $defaultNote,
                'created_by'  => $request->user()->id,
            ]);
        });

        Cache::forget("dashboard:{$account->id}:" . now($account->timezone ?? 'UTC')->format('Y-m-d-H'));

        return back()->with(
            'success',
            sprintf(
                '%s adjustment of %s created on %s.',
                ucfirst($type),
                number_format($amount, 2),
                $wallet->name
            )
        );
    }
}
