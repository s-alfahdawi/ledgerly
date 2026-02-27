<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'transactions:process-recurring';
    protected $description = 'Generate transactions from recurring templates that are due';

    public function handle(): int
    {
        $due = RecurringTransaction::active()
            ->due()
            ->with(['wallet', 'category'])
            ->get();

        if ($due->isEmpty()) {
            $this->info('No recurring transactions due.');
            return self::SUCCESS;
        }

        $generated = 0;
        $errors = 0;

        foreach ($due as $recurring) {
            try {
                DB::transaction(function () use ($recurring, &$generated) {
                    // Generate transactions for all missed dates up to today
                    while ($recurring->next_due_date->lte(now())) {
                        Transaction::create([
                            'account_id' => $recurring->account_id,
                            'wallet_id' => $recurring->wallet_id,
                            'category_id' => $recurring->category_id,
                            'type' => $recurring->type,
                            'amount' => $recurring->amount,
                            'occurred_at' => $recurring->next_due_date,
                            'note' => $recurring->note,
                            'created_by' => $recurring->created_by,
                        ]);
                        $generated++;

                        $nextDate = $recurring->calculateNextDueDate();
                        if ($nextDate === null) {
                            // Schedule ended
                            $recurring->update([
                                'is_active' => false,
                                'last_generated_at' => $recurring->next_due_date,
                            ]);
                            return;
                        }

                        $recurring->update([
                            'last_generated_at' => $recurring->next_due_date,
                            'next_due_date' => $nextDate,
                        ]);
                        $recurring->refresh();
                    }
                });
            } catch (\Throwable $e) {
                $errors++;
                Log::error("Failed to process recurring transaction #{$recurring->id}: {$e->getMessage()}");
                $this->error("Failed: #{$recurring->id} - {$e->getMessage()}");
            }
        }

        $this->info("Processed: {$generated} transactions generated, {$errors} errors.");
        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
