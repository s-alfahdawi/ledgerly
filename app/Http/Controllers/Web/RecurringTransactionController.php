<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Wallet;
use App\Services\AccountContext;
use Illuminate\Http\Request;

class RecurringTransactionController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index()
    {
        $accountId = $this->requireAccountId();

        $recurring = RecurringTransaction::forAccount($accountId)
            ->with(['wallet', 'category', 'creator'])
            ->orderBy('is_active', 'desc')
            ->orderBy('next_due_date')
            ->get();

        return view('recurring.index', compact('recurring'));
    }

    public function create()
    {
        $accountId = $this->requireAccountId();

        $wallets = Wallet::forAccount($accountId)->active()->get();
        $categories = Category::forAccount($accountId)->active()->get();

        return view('recurring.create', compact('wallets', 'categories'));
    }

    public function store(Request $request)
    {
        $accountId = $this->requireAccountId();

        $data = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'note' => ['nullable', 'string', 'max:255'],
            'frequency' => ['required', 'in:daily,weekly,biweekly,monthly,yearly'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        Wallet::forAccount($accountId)->findOrFail($data['wallet_id']);
        if ($data['category_id']) {
            Category::forAccount($accountId)->findOrFail($data['category_id']);
        }

        RecurringTransaction::create([
            'account_id' => $accountId,
            'wallet_id' => $data['wallet_id'],
            'category_id' => $data['category_id'] ?? null,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
            'frequency' => $data['frequency'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'next_due_date' => $data['start_date'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('recurring.index')->with('success', 'Recurring transaction created.');
    }

    public function edit(RecurringTransaction $recurring)
    {
        $accountId = $this->requireAccountId();
        if ($recurring->account_id !== $accountId) {
            abort(403);
        }

        $wallets = Wallet::forAccount($accountId)->active()->get();
        $categories = Category::forAccount($accountId)->active()->get();

        return view('recurring.edit', compact('recurring', 'wallets', 'categories'));
    }

    public function update(Request $request, RecurringTransaction $recurring)
    {
        $accountId = $this->requireAccountId();
        if ($recurring->account_id !== $accountId) {
            abort(403);
        }

        $data = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'note' => ['nullable', 'string', 'max:255'],
            'frequency' => ['required', 'in:daily,weekly,biweekly,monthly,yearly'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        Wallet::forAccount($accountId)->findOrFail($data['wallet_id']);
        if ($data['category_id']) {
            Category::forAccount($accountId)->findOrFail($data['category_id']);
        }

        $recurring->update([
            'wallet_id' => $data['wallet_id'],
            'category_id' => $data['category_id'] ?? null,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
            'frequency' => $data['frequency'],
            'end_date' => $data['end_date'] ?? null,
            'is_active' => $data['is_active'] ?? $recurring->is_active,
        ]);

        return redirect()->route('recurring.index')->with('success', 'Recurring transaction updated.');
    }

    public function destroy(RecurringTransaction $recurring)
    {
        $accountId = $this->requireAccountId();
        if ($recurring->account_id !== $accountId) {
            abort(403);
        }

        $recurring->delete();

        return redirect()->route('recurring.index')->with('success', 'Recurring transaction deleted.');
    }

    public function toggle(RecurringTransaction $recurring)
    {
        $accountId = $this->requireAccountId();
        if ($recurring->account_id !== $accountId) {
            abort(403);
        }

        $recurring->update(['is_active' => !$recurring->is_active]);
        $status = $recurring->is_active ? 'activated' : 'paused';

        return redirect()->route('recurring.index')->with('success', "Recurring transaction {$status}.");
    }
}
