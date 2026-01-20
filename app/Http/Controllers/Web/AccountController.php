<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AccountContext;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    /**
     * Switch the current account context.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
        ]);

        $accountId = $request->account_id;
        $user = $request->user();

        // Verify user is a member of the account
        if (!$user->accounts()->where('accounts.id', $accountId)->exists()) {
            return redirect()->back()->with('error', 'You do not have access to that account.');
        }

        $this->accountContext->switch($accountId);

        return redirect()->back();
    }
}
