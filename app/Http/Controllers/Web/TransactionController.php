<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\AccountContext;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private AccountContext $accountContext
    ) {
    }

    public function index(Request $request)
    {
        $accountId = $this->requireAccountId();

        $query = Transaction::forAccount($accountId)
            ->with(['wallet', 'category', 'creator', 'tags']);

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

        // Filter by tag
        if ($request->filled('tag_id')) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $request->tag_id));
        }

        // Sorting - default to occurred_at desc if no sort specified
        $sortField = $request->get('sort', 'occurred_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['occurred_at', 'amount', 'type', 'created_at'];

        // Only apply sorting if sort field is in allowed list
        if (in_array($sortField, $allowedSorts)) {
            $query->reorder($sortField, $sortDirection);
        } else {
            $query->reorder('occurred_at', 'desc');
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accountId = $this->requireAccountId();
        $wallets = Wallet::forAccount($accountId)->active()->get();
        $categories = Category::forAccount($accountId)->active()->get();
        $tags = Tag::forAccount($accountId)->orderBy('name')->get();

        return view('transactions.create', compact('wallets', 'categories', 'tags'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $accountId = $this->requireAccountId();
        $data = $request->validated();
        $data['account_id'] = $accountId;
        $data['created_by'] = $request->user()->id;

        if ($data['type'] === 'transfer' && $request->filled('to_wallet_id')) {
            $data['to_wallet_id'] = $request->to_wallet_id;
            $transactions = $this->transactionService->createTransfer($data);
            $transaction = $transactions[0];
        } else {
            $transaction = $this->transactionService->create($data);
        }

        // Sync tags
        if ($request->has('tags')) {
            $tagIds = Tag::forAccount($accountId)->whereIn('id', $request->input('tags', []))->pluck('id');
            $transaction->tags()->sync($tagIds);
        }

        // Handle receipt/attachment uploads
        $this->handleAttachments($request, $transaction, $accountId);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        $transaction->load(['wallet', 'category', 'creator', 'updater', 'attachments']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $accountId = $this->requireAccountId();
        $wallets = Wallet::forAccount($accountId)->active()->get();
        $categories = Category::forAccount($accountId)->active()->get();
        $tags = Tag::forAccount($accountId)->orderBy('name')->get();
        $transaction->load(['tags', 'attachments']);

        return view('transactions.edit', compact('transaction', 'wallets', 'categories', 'tags'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        $this->transactionService->update($transaction, $data);

        // Sync tags
        $accountId = $this->requireAccountId();
        $tagIds = Tag::forAccount($accountId)->whereIn('id', $request->input('tags', []))->pluck('id');
        $transaction->tags()->sync($tagIds);

        // Handle receipt/attachment uploads
        $this->handleAttachments($request, $transaction, $accountId);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    private function handleAttachments(Request $request, Transaction $transaction, int $accountId): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        $request->validate([
            'attachments' => ['array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt'],
        ]);

        foreach ($request->file('attachments') as $file) {
            $path = $file->store("attachments/{$accountId}/{$transaction->id}", 'local');

            Attachment::create([
                'account_id' => $accountId,
                'attachable_id' => $transaction->id,
                'attachable_type' => Transaction::class,
                'filename' => $file->getClientOriginalName(),
                'disk_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $this->transactionService->delete($transaction, auth()->id());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function duplicate(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        $accountId = $this->requireAccountId();

        $wallets = Wallet::forAccount($accountId)->active()->get();
        $categories = Category::forAccount($accountId)->active()->get();
        $tags = Tag::forAccount($accountId)->orderBy('name')->get();

        // Pre-fill with original transaction data but set today's date
        $duplicate = $transaction->replicate();
        $duplicate->occurred_at = now();

        return view('transactions.create', [
            'wallets' => $wallets,
            'categories' => $categories,
            'tags' => $tags,
            'duplicate' => $transaction,
        ]);
    }

    public function bulkAction(Request $request)
    {
        $accountId = $this->requireAccountId();

        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'action' => ['required', 'in:delete,set_category,add_tag,remove_tag'],
        ]);

        $ids = $request->input('ids');
        $transactions = Transaction::forAccount($accountId)->whereIn('id', $ids)->get();

        if ($transactions->isEmpty()) {
            return back()->with('error', 'No matching transactions found.');
        }

        $count = $transactions->count();

        switch ($request->action) {
            case 'delete':
                DB::transaction(function () use ($transactions) {
                    $userId = auth()->id();
                    foreach ($transactions as $txn) {
                        $this->transactionService->delete($txn, $userId);
                    }
                });
                return back()->with('success', "{$count} transactions deleted.");

            case 'set_category':
                $request->validate(['category_id' => ['required', 'exists:categories,id']]);
                Category::forAccount($accountId)->findOrFail($request->category_id);
                Transaction::forAccount($accountId)->whereIn('id', $ids)->update([
                    'category_id' => $request->category_id,
                    'updated_by' => auth()->id(),
                ]);
                return back()->with('success', "Category updated on {$count} transactions.");

            case 'add_tag':
                $request->validate(['tag_id' => ['required', 'exists:tags,id']]);
                $tag = Tag::forAccount($accountId)->findOrFail($request->tag_id);
                foreach ($transactions as $txn) {
                    $txn->tags()->syncWithoutDetaching([$tag->id]);
                }
                return back()->with('success', "Tag \"{$tag->name}\" added to {$count} transactions.");

            case 'remove_tag':
                $request->validate(['tag_id' => ['required', 'exists:tags,id']]);
                $tag = Tag::forAccount($accountId)->findOrFail($request->tag_id);
                foreach ($transactions as $txn) {
                    $txn->tags()->detach($tag->id);
                }
                return back()->with('success', "Tag \"{$tag->name}\" removed from {$count} transactions.");
        }

        return back();
    }
}
