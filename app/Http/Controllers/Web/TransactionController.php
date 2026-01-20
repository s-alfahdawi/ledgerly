<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\AccountContext;
use App\Services\TransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private TransactionService $transactionService,
        private AccountContext $accountContext
    ) {
    }

    public function index(Request $request)
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        
        $query = Transaction::forAccount($accountId)
            ->with(['wallet', 'category', 'creator']);

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
        } elseif ($request->filled('start_date')) {
            $query->where('occurred_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('occurred_at', '<=', $request->end_date);
        }
        
        // Search by note
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('note', 'like', "%{$search}%");
        }

        // Sorting - default to occurred_at desc if no sort specified
        $sortField = $request->get('sort', 'occurred_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['occurred_at', 'amount', 'type', 'created_at'];
        
        // Only apply sorting if sort field is in allowed list
        if (in_array($sortField, $allowedSorts)) {
            $query->reorder($sortField, $sortDirection); // Use reorder() to replace any existing orderBy
        } else {
            $query->reorder('occurred_at', 'desc');
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        $wallets = \App\Models\Wallet::forAccount($accountId)->active()->get();
        $categories = \App\Models\Category::forAccount($accountId)->active()->get();

        return view('transactions.create', compact('wallets', 'categories'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        $data = $request->validated();
        $data['account_id'] = $accountId;
        $data['created_by'] = $request->user()->id;

        if ($data['type'] === 'transfer' && $request->filled('to_wallet_id')) {
            $data['to_wallet_id'] = $request->to_wallet_id;
            $this->transactionService->createTransfer($data);
        } else {
            $this->transactionService->create($data);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        
        $transaction->load(['wallet', 'category', 'creator', 'updater']);
        
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        $wallets = \App\Models\Wallet::forAccount($accountId)->active()->get();
        $categories = \App\Models\Category::forAccount($accountId)->active()->get();

        return view('transactions.edit', compact('transaction', 'wallets', 'categories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        $this->transactionService->update($transaction, $data);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        
        $this->transactionService->delete($transaction, auth()->id());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
}
