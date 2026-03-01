<?php

namespace App\Http\Controllers\Web;

use App\Enums\Currency;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountContext;
use App\Services\ReportService;
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

            $incomeWalletTotals = $this->reportService->getWalletIncomeTotals($accountId);
            $expenseCategoryTotals = $this->reportService->getCategoryTotals($accountId, 'expense');

            $last12Months = $this->reportService->getMonthlyTrend($accountId, 12, $timezone);

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
                'walletBalances', 'totalBalance', 'last12Months',
                'incomeWalletTotals', 'expenseCategoryTotals',
                'budgetProgress', 'recentTransactions',
                'transactionCount', 'lastMonthTxCount'
            );
        });

        return view('dashboard.index', array_merge($data, [
            'account' => $account,
            'monthlyData' => $data['last12Months'],
        ]));
    }
}
