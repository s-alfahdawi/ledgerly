<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Services\AccountContext;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        
        $query = Category::forAccount($accountId)->active();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Filter by type if provided
        if ($request->filled('type')) {
            $query->byType($request->type);
        }
        
        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $allowedSorts = ['name', 'type', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->reorder($sortField, $sortDirection); // Use reorder() to replace any existing orderBy
        } else {
            $query->reorder('name', 'asc');
        }
        
        $categories = $query->paginate(15)->withQueryString();
        
        $account = $this->accountContext->account();

        return view('categories.index', compact('categories', 'account'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        $data = $request->validated();
        $data['account_id'] = $accountId;

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        
        return view('categories.edit', compact('category'));
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);
        
        $category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        
        $category->update(['deleted_by' => auth()->id()]);
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
