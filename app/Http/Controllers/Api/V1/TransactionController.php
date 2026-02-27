<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\AccountContext;
use App\Services\TransactionService;
use Illuminate\Http\Request;

/**
 * TransactionController (API)
 * 
 * API endpoints for transaction management.
 * IMPORTANT: This controller only uses services - no web-specific logic (sessions, redirects, etc.).
 * This ensures the API is ready for mobile app integration.
 */
class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private AccountContext $accountContext
    ) {
    }

    public function index(Request $request)
    {
        $accountId = $this->accountContext->id();
        
        $query = Transaction::forAccount($accountId)
            ->with(['wallet', 'category', 'creator'])
            ->orderBy('occurred_at', 'desc');

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('occurred_at', [$request->start_date, $request->end_date]);
        }

        return TransactionResource::collection($query->paginate(20));
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $data['account_id'] = $this->accountContext->id();
        $data['created_by'] = $request->user()->id;

        if ($data['type'] === 'transfer' && $request->filled('to_wallet_id')) {
            $data['to_wallet_id'] = $request->to_wallet_id;
            $transactions = $this->transactionService->createTransfer($data);
            return TransactionResource::collection(collect($transactions))->response()->setStatusCode(201);
        }

        $transaction = $this->transactionService->create($data);
        return (new TransactionResource($transaction))->response()->setStatusCode(201);
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        $transaction->load(['wallet', 'category', 'creator', 'updater']);
        return new TransactionResource($transaction);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        $transaction = $this->transactionService->update($transaction, $data);
        return new TransactionResource($transaction);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        $this->transactionService->delete($transaction, $request->user()->id);
        return response()->json(null, 204);
    }
}
