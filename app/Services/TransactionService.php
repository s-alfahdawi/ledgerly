<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

/**
 * TransactionService
 * 
 * Handles all transaction-related business logic including transfers.
 * 
 * TRANSFER INVARIANTS:
 * - Transfers are represented by two transactions sharing a common transfer_ref (UUID)
 * - One transaction leg is type='expense' (from_wallet), one is type='income' (to_wallet)
 * - Both legs have the same amount and occurred_at timestamp
 * - Both legs are created atomically within a single DB transaction
 * - Deleting one transfer leg always deletes the pair inside a DB transaction
 * - transfer_ref is used to link the two legs together
 */
class TransactionService
{
    public function create(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = Transaction::create($data);

            if ($data['type'] === 'transfer' && isset($data['to_wallet_id'])) {
                $this->createTransferLeg($data, $transaction);
            }

            return $transaction;
        });
    }

    /**
     * Update a transaction.
     * 
     * Note: Transactions older than config('billing.transaction_edit_days') cannot be updated.
     * Use reverse transaction feature for old transactions instead.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        // Check immutability rule
        $editDays = config('billing.transaction_edit_days');
        if ($editDays !== null) {
            $cutoffDate = now()->subDays($editDays);
            if ($transaction->occurred_at->lt($cutoffDate)) {
                throw new \Exception("Transactions older than {$editDays} days cannot be edited. Use reverse transaction instead.");
            }
        }

        return DB::transaction(function () use ($transaction, $data) {
            $wasTransfer = $transaction->type === 'transfer';
            $isTransfer = $data['type'] === 'transfer';

            if ($wasTransfer && $transaction->transfer_ref) {
                Transaction::where('transfer_ref', $transaction->transfer_ref)
                    ->where('id', '!=', $transaction->id)
                    ->delete();
            }

            $transaction->update($data);

            if ($isTransfer && isset($data['to_wallet_id'])) {
                $this->createTransferLeg($data, $transaction);
            }

            return $transaction->fresh();
        });
    }

    /**
     * Delete a transaction.
     * 
     * If the transaction is part of a transfer (has transfer_ref),
     * both legs are deleted atomically to maintain data integrity.
     * Uses soft deletes and sets deleted_by for audit trail.
     */
    public function delete(Transaction $transaction, int $deletedBy): void
    {
        DB::transaction(function () use ($transaction, $deletedBy) {
            if ($transaction->transfer_ref) {
                // Delete both transfer legs atomically (soft delete)
                Transaction::where('transfer_ref', $transaction->transfer_ref)
                    ->update(['deleted_by' => $deletedBy]);
                Transaction::where('transfer_ref', $transaction->transfer_ref)->delete();
            } else {
                $transaction->update(['deleted_by' => $deletedBy]);
                $transaction->delete();
            }
        });
    }

    /**
     * Create a transfer between two wallets.
     * 
     * Creates two transaction legs atomically:
     * - Expense leg: from_wallet_id with type='expense'
     * - Income leg: to_wallet_id with type='income'
     * Both share the same transfer_ref for linking.
     */
    public function createTransfer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $transferRef = uniqid('transfer_');

            $fromTransaction = Transaction::create([
                'account_id' => $data['account_id'],
                'wallet_id' => $data['from_wallet_id'],
                'category_id' => null,
                'type' => 'expense',
                'amount' => $data['amount'],
                'occurred_at' => $data['occurred_at'],
                'note' => $data['note'] ?? 'Transfer',
                'created_by' => $data['created_by'],
                'transfer_ref' => $transferRef,
            ]);

            $toTransaction = Transaction::create([
                'account_id' => $data['account_id'],
                'wallet_id' => $data['to_wallet_id'],
                'category_id' => null,
                'type' => 'income',
                'amount' => $data['amount'],
                'occurred_at' => $data['occurred_at'],
                'note' => $data['note'] ?? 'Transfer',
                'created_by' => $data['created_by'],
                'transfer_ref' => $transferRef,
            ]);

            return [$fromTransaction, $toTransaction];
        });
    }

    private function createTransferLeg(array $data, Transaction $transaction): Transaction
    {
        $transferRef = $transaction->transfer_ref ?? uniqid('transfer_');
        $transaction->update(['transfer_ref' => $transferRef]);

        $oppositeType = $transaction->type === 'expense' ? 'income' : 'expense';
        $oppositeWalletId = $transaction->type === 'expense' 
            ? $data['to_wallet_id'] 
            : $data['from_wallet_id'];

        return Transaction::create([
            'account_id' => $transaction->account_id,
            'wallet_id' => $oppositeWalletId,
            'category_id' => null,
            'type' => $oppositeType,
            'amount' => $transaction->amount,
            'occurred_at' => $transaction->occurred_at,
            'note' => $transaction->note ?? 'Transfer',
            'created_by' => $transaction->created_by,
            'transfer_ref' => $transferRef,
        ]);
    }
}
