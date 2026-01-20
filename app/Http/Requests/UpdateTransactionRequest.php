<?php

namespace App\Http\Requests;

use App\Models\Wallet;
use App\Models\Category;
use App\Services\AccountContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('transaction'));
    }

    public function rules(): array
    {
        $accountId = app(AccountContext::class)->id();

        return [
            'wallet_id' => ['required', 'exists:wallets,id', function ($attribute, $value, $fail) use ($accountId) {
                if (!Wallet::forAccount($accountId)->where('id', $value)->exists()) {
                    $fail('The selected wallet does not belong to your account.');
                }
            }],
            'category_id' => ['nullable', 'exists:categories,id', function ($attribute, $value, $fail) use ($accountId) {
                if ($value && !Category::forAccount($accountId)->where('id', $value)->exists()) {
                    $fail('The selected category does not belong to your account.');
                }
            }],
            'type' => ['required', Rule::in(['income', 'expense', 'transfer'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'occurred_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
