<?php

namespace App\Http\Requests\Concerns;

use App\Models\Category;
use App\Models\Wallet;
use App\Services\AccountContext;
use Illuminate\Validation\Rule;

trait TransactionRules
{
    protected function transactionRules(): array
    {
        $accountId = app(AccountContext::class)->id();
        $type = $this->input('type', 'expense');

        $walletOwnership = function ($attribute, $value, $fail) use ($accountId) {
            if ($value && !Wallet::forAccount($accountId)->where('id', $value)->exists()) {
                $fail('The selected wallet does not belong to your account.');
            }
        };

        $categoryOwnership = function ($attribute, $value, $fail) use ($accountId) {
            if ($value && !Category::forAccount($accountId)->where('id', $value)->exists()) {
                $fail('The selected category does not belong to your account.');
            }
        };

        $walletRequired = in_array($type, ['income', 'transfer']) ? 'required' : 'nullable';
        $categoryRequired = $type === 'expense' ? 'required' : 'nullable';

        $rules = [
            'wallet_id'    => [$walletRequired, 'exists:wallets,id', $walletOwnership],
            'category_id'  => [$categoryRequired, 'exists:categories,id', $categoryOwnership],
            'type'         => ['required', Rule::in(['income', 'expense', 'transfer'])],
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'occurred_at'  => ['required', 'date'],
            'note'         => ['nullable', 'string', 'max:1000'],
        ];

        if ($type === 'transfer') {
            $rules['to_wallet_id'] = ['required', 'exists:wallets,id', $walletOwnership];
        }

        return $rules;
    }
}
