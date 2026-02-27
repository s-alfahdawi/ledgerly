<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\AccountContext;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index(Request $request)
    {
        $accountId = $this->requireAccountId();

        $query = $request->input('q', '');

        if (strlen(trim($query)) < 1) {
            return view('search.index', [
                'query' => $query,
                'transactions' => collect(),
                'wallets' => collect(),
                'categories' => collect(),
                'tags' => collect(),
                'hasResults' => false,
            ]);
        }

        $search = trim($query);

        $transactions = Transaction::search($search)
            ->where('account_id', $accountId)
            ->query(fn ($q) => $q->with(['wallet', 'category', 'tags']))
            ->take(20)
            ->get();

        if (is_numeric($search) && $transactions->isEmpty()) {
            $transactions = Transaction::forAccount($accountId)
                ->with(['wallet', 'category', 'tags'])
                ->where('amount', $search)
                ->orderBy('occurred_at', 'desc')
                ->limit(20)
                ->get();
        }

        $wallets = Wallet::search($search)
            ->where('account_id', $accountId)
            ->take(10)
            ->get();

        $categories = Category::search($search)
            ->where('account_id', $accountId)
            ->take(10)
            ->get();

        $tags = Tag::search($search)
            ->where('account_id', $accountId)
            ->take(10)
            ->get();

        $hasResults = $transactions->isNotEmpty()
            || $wallets->isNotEmpty()
            || $categories->isNotEmpty()
            || $tags->isNotEmpty();

        return view('search.index', compact(
            'query',
            'transactions',
            'wallets',
            'categories',
            'tags',
            'hasResults'
        ));
    }
}
