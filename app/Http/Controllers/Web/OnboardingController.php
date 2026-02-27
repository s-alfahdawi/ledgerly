<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Wallet;
use App\Services\AccountContext;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index()
    {
        $account = $this->accountContext->account();
        if (!$account) {
            return redirect()->route('dashboard');
        }

        $hasWallets = Wallet::forAccount($account->id)->exists();
        $hasCategories = Category::forAccount($account->id)->exists();

        if ($hasWallets && $hasCategories) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.index', [
            'account' => $account,
            'hasWallets' => $hasWallets,
            'hasCategories' => $hasCategories,
        ]);
    }

    public function setupAccount(Request $request)
    {
        $account = $this->accountContext->account();
        if (!$account) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency_code' => ['required', 'string', 'in:IQD,USD,EUR,GBP,AED,SAR,TRY'],
            'timezone' => ['required', 'string', 'timezone'],
        ]);

        $account->update($request->only('name', 'currency_code', 'timezone'));

        return redirect()->route('onboarding.index')->with('step', 'wallets');
    }

    public function setupWallets(Request $request)
    {
        $account = $this->accountContext->account();
        if (!$account) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'wallets' => ['required', 'array', 'min:1'],
            'wallets.*.name' => ['required', 'string', 'max:255'],
            'wallets.*.type' => ['required', 'string', 'in:cash,bank,mobile_money,other'],
            'wallets.*.opening_balance' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($request->wallets as $walletData) {
            Wallet::create([
                'account_id' => $account->id,
                'name' => $walletData['name'],
                'type' => $walletData['type'],
                'opening_balance' => $walletData['opening_balance'] ?? 0,
            ]);
        }

        return redirect()->route('onboarding.index')->with('step', 'categories');
    }

    public function setupCategories(Request $request)
    {
        $account = $this->accountContext->account();
        if (!$account) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'categories' => ['required', 'array', 'min:1'],
            'categories.*.name' => ['required', 'string', 'max:255'],
            'categories.*.type' => ['required', 'string', 'in:income,expense,both'],
            'categories.*.color' => ['nullable', 'string', 'max:7'],
        ]);

        $colors = ['#405189', '#0ab39c', '#f06548', '#f7b84b', '#299cdb', '#6c757d', '#e83e8c', '#6610f2'];
        $colorIndex = 0;

        foreach ($request->categories as $catData) {
            Category::create([
                'account_id' => $account->id,
                'name' => $catData['name'],
                'type' => $catData['type'],
                'color' => $catData['color'] ?? $colors[$colorIndex % count($colors)],
            ]);
            $colorIndex++;
        }

        return redirect()->route('dashboard')->with('success', 'Your account is all set up! Start tracking your finances.');
    }

    public function skip()
    {
        $account = $this->accountContext->account();
        if (!$account) {
            return redirect()->route('dashboard');
        }

        // Create minimal defaults so the user can use the app
        $hasWallets = Wallet::forAccount($account->id)->exists();
        $hasCategories = Category::forAccount($account->id)->exists();

        if (!$hasWallets) {
            Wallet::create([
                'account_id' => $account->id,
                'name' => 'Cash',
                'type' => 'cash',
                'opening_balance' => 0,
            ]);
        }

        if (!$hasCategories) {
            $defaults = [
                ['name' => 'Salary', 'type' => 'income', 'color' => '#0ab39c'],
                ['name' => 'General', 'type' => 'expense', 'color' => '#405189'],
            ];
            foreach ($defaults as $cat) {
                Category::create(array_merge($cat, ['account_id' => $account->id]));
            }
        }

        return redirect()->route('dashboard')->with('success', 'Default wallet and categories created. You can customize them in Settings.');
    }
}
