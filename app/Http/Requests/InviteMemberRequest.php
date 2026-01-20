<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Check if user can invite members using MemberPolicy
        $accountContext = app(\App\Services\AccountContext::class);
        $accountId = $accountContext->id();
        if (!$accountId) {
            return false;
        }
        
        $user = $this->user();
        $membership = $user->accounts()->where('accounts.id', $accountId)->first();
        if (!$membership) {
            return false;
        }
        
        $role = $membership->pivot->role;
        return in_array($role, ['owner', 'admin']);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'role' => ['required', Rule::in(['admin', 'member', 'viewer'])],
        ];
    }
}
