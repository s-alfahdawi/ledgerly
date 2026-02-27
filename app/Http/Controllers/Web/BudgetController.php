<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Category;
use App\Services\AccountContext;
use App\Services\ReportService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct(
        private AccountContext $accountContext,
        private ReportService $reportService
    ) {
    }

    public function index()
    {
        $accountId = $this->requireAccountId();

        $account = $this->accountContext->account();
        $timezone = $account->timezone ?? 'UTC';

        $budgetProgress = $this->reportService->getBudgetProgress($accountId, $timezone);
        $budgets = Budget::forAccount($accountId)->with('category')->get();

        return view('budgets.index', compact('budgetProgress', 'budgets'));
    }

    public function create()
    {
        $accountId = $this->requireAccountId();

        $categories = Category::forAccount($accountId)->active()->byType('expense')->get();
        $existingCategoryIds = Budget::forAccount($accountId)->pluck('category_id')->filter()->toArray();

        return view('budgets.create', compact('categories', 'existingCategoryIds'));
    }

    public function store(Request $request)
    {
        $accountId = $this->requireAccountId();

        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'period' => ['required', 'in:monthly,weekly,yearly'],
        ]);

        if ($data['category_id']) {
            Category::forAccount($accountId)->findOrFail($data['category_id']);
        }

        // Check for duplicate
        $exists = Budget::forAccount($accountId)
            ->where('category_id', $data['category_id'] ?? null)
            ->where('period', $data['period'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'A budget for this category and period already exists.');
        }

        Budget::create([
            'account_id' => $accountId,
            'category_id' => $data['category_id'] ?? null,
            'amount' => $data['amount'],
            'period' => $data['period'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget created.');
    }

    public function edit(Budget $budget)
    {
        $accountId = $this->requireAccountId();
        if ($budget->account_id !== $accountId) {
            abort(403);
        }

        $categories = Category::forAccount($accountId)->active()->byType('expense')->get();

        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $accountId = $this->requireAccountId();
        if ($budget->account_id !== $accountId) {
            abort(403);
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $budget->update([
            'amount' => $data['amount'],
            'is_active' => $data['is_active'] ?? $budget->is_active,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget updated.');
    }

    public function destroy(Budget $budget)
    {
        $accountId = $this->requireAccountId();
        if ($budget->account_id !== $accountId) {
            abort(403);
        }

        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted.');
    }
}
