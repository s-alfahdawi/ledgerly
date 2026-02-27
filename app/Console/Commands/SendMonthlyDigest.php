<?php

namespace App\Console\Commands;

use App\Mail\MonthlyDigestMail;
use App\Models\Account;
use App\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMonthlyDigest extends Command
{
    protected $signature = 'notifications:monthly-digest
                            {--account= : Send for a specific account ID}
                            {--dry-run : Preview without sending}';

    protected $description = 'Send monthly financial digest emails to all account owners';

    public function __construct(
        private ReportService $reportService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $query = Account::with('owner');

        if ($accountId = $this->option('account')) {
            $query->where('id', $accountId);
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            $this->warn('No accounts found.');
            return self::SUCCESS;
        }

        $sent = 0;
        $lastMonth = now()->subMonth();
        $month = $lastMonth->month;
        $year = $lastMonth->year;
        $monthLabel = $lastMonth->format('F Y');

        foreach ($accounts as $account) {
            $owner = $account->owner;
            if (!$owner || !$owner->email) {
                $this->warn("Skipping account #{$account->id} — no owner email.");
                continue;
            }

            $timezone = $account->timezone ?? 'UTC';

            $summary = $this->reportService->getMonthlySummary(
                $account->id,
                $month,
                $year,
                $timezone
            );

            $topCategories = $this->reportService->getTopCategories(
                $account->id,
                5,
                $lastMonth->copy()->startOfMonth()->toDateString(),
                $lastMonth->copy()->endOfMonth()->toDateString(),
                $timezone
            );

            $walletBalances = $this->reportService->getWalletBalances($account->id);

            if ($this->option('dry-run')) {
                $this->info("[DRY RUN] Would send digest to {$owner->email} for {$account->name} ({$monthLabel})");
                $this->info("  Income: {$summary['income']}, Expense: {$summary['expense']}, Net: {$summary['net']}");
                $sent++;
                continue;
            }

            Mail::to($owner->email)->send(
                new MonthlyDigestMail(
                    user: $owner,
                    account: $account,
                    summary: $summary,
                    topCategories: $topCategories,
                    walletBalances: $walletBalances,
                    monthLabel: $monthLabel,
                )
            );

            $sent++;
            $this->info("Sent digest to {$owner->email} for {$account->name}");
        }

        $this->info("Monthly digest sent to {$sent} account(s).");
        return self::SUCCESS;
    }
}
