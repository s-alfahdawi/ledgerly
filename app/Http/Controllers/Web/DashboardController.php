<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountContext;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private AccountContext $accountContext,
        private ReportService $reportService
    ) {
    }

    /**
     * Create a default account for a user who doesn't have one.
     */
    protected function createDefaultAccount($user): ?Account
    {
        return DB::transaction(function () use ($user) {
            $account = Account::create([
                'name' => $user->name . "'s Account",
                'currency_code' => 'IQD', // Default currency
                'timezone' => 'Asia/Baghdad', // Default timezone
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
        
        // Try to resolve account automatically
        $account = $this->accountContext->resolve();
        
        // If user has no accounts, create one automatically
        if (!$account && $user) {
            $account = $this->createDefaultAccount($user);
            if ($account) {
                $this->accountContext->set($account->id);
            }
        }
        
        if (!$account) {
            // User has no accounts - show a helpful message
            return view('dashboard.no-account', [
                'user' => $user,
            ]);
        }

        $accountId = $account->id;
        $timezone = $account->timezone ?? 'UTC';
        $now = now($timezone);

        $summary = $this->reportService->getMonthlySummary(
            $accountId,
            $now->month,
            $now->year,
            $timezone
        );

        $topCategories = $this->reportService->getTopCategories(
            $accountId,
            5,
            $now->startOfMonth()->toDateString(),
            $now->endOfMonth()->toDateString(),
            $timezone
        );

        $walletBalances = $this->reportService->getWalletBalances($accountId);
        $totalBalance = array_sum(array_column($walletBalances, 'balance'));

        // Get last 12 months data for charts
        $last12Months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthSummary = $this->reportService->getMonthlySummary(
                $accountId,
                $date->month,
                $date->year,
                $timezone
            );
            $last12Months[] = [
                'month' => $date->format('M Y'),
                'income' => $monthSummary['income'],
                'expense' => $monthSummary['expense'],
                'net' => $monthSummary['net'],
            ];
        }

        return view('dashboard.index', [
            'summary' => $summary,
            'topCategories' => $topCategories,
            'walletBalances' => $walletBalances,
            'totalBalance' => $totalBalance,
            'monthlyData' => $last12Months,
            'account' => $account,
        ]);
    }
}
