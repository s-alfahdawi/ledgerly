<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWalletRequest;
use App\Models\Wallet;
use App\Services\AccountContext;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Wallet::class);
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        
        $query = Wallet::forAccount($accountId)->active()->with('transactions');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
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
        
        $wallets = $query->paginate(15)->withQueryString();

        return view('wallets.index', compact('wallets'));
    }

    public function create()
    {
        return view('wallets.create');
    }

    public function store(StoreWalletRequest $request)
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        $data = $request->validated();
        $data['account_id'] = $accountId;

        Wallet::create($data);

        return redirect()->route('wallets.index')
            ->with('success', 'Wallet created successfully.');
    }

    public function edit(Wallet $wallet)
    {
        $this->authorize('update', $wallet);
        
        return view('wallets.edit', compact('wallet'));
    }

    public function update(StoreWalletRequest $request, Wallet $wallet)
    {
        $this->authorize('update', $wallet);
        
        $wallet->update($request->validated());

        return redirect()->route('wallets.index')
            ->with('success', 'Wallet updated successfully.');
    }

    public function destroy(Wallet $wallet)
    {
        $this->authorize('delete', $wallet);
        
        $wallet->update(['deleted_by' => auth()->id()]);
        $wallet->delete();

        return redirect()->route('wallets.index')
            ->with('success', 'Wallet deleted successfully.');
    }
}
