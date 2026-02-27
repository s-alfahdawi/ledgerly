<?php

namespace App\Console\Commands;

use App\Enums\Currency;
use App\Mail\LowBalanceAlertMail;
use App\Models\Account;
use App\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendLowBalanceAlerts extends Command
{
    protected $signature = 'notifications:low-balance
                            {--threshold=0 : Balance threshold to trigger alert}
                            {--account= : Check a specific account ID}
                            {--dry-run : Preview without sending}';

    protected $description = 'Send low balance alerts when wallet balances drop below threshold';

    /**
     * Default threshold: 0 (alert when any wallet reaches zero or negative).
     * Can be overridden per-run with --threshold.
     */
    private const DEFAULT_THRESHOLD = 0;

    public function __construct(
        private ReportService $reportService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $threshold = (float) $this->option('threshold') ?: self::DEFAULT_THRESHOLD;

        $query = Account::with('owner');

        if ($accountId = $this->option('account')) {
            $query->where('id', $accountId);
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            $this->warn('No accounts found.');
            return self::SUCCESS;
        }

        $alertsSent = 0;

        foreach ($accounts as $account) {
            $owner = $account->owner;
            if (!$owner || !$owner->email) {
                continue;
            }

            $walletBalances = $this->reportService->getWalletBalances($account->id);
            $currencyCode = $account->currency_code ?? Currency::IQD->value;

            // Find wallets at or below threshold
            $lowWallets = array_filter($walletBalances, function ($w) use ($threshold) {
                return $w['balance'] <= $threshold;
            });

            if (empty($lowWallets)) {
                continue;
            }

            if ($this->option('dry-run')) {
                $this->info("[DRY RUN] Would alert {$owner->email} for {$account->name}:");
                foreach ($lowWallets as $w) {
                    $this->warn("  {$w['wallet']}: {$w['balance']} {$currencyCode}");
                }
                $alertsSent++;
                continue;
            }

            Mail::to($owner->email)->send(
                new LowBalanceAlertMail(
                    user: $owner,
                    account: $account,
                    lowWallets: array_values($lowWallets),
                    currencyCode: $currencyCode,
                )
            );

            $alertsSent++;
            $this->info("Alert sent to {$owner->email} for {$account->name} (" . count($lowWallets) . " low wallets)");
        }

        $this->info("Low balance alerts sent for {$alertsSent} account(s).");
        return self::SUCCESS;
    }
}
