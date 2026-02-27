<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountContext;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index()
    {
        $accountId = $this->requireAccountId();

        if (!auth()->user()->hasPermissionInAccount($accountId, 'settings.view')) {
            abort(403, 'You do not have permission to view settings.');
        }

        $account = Account::findOrFail($accountId);

        return view('settings.index', compact('account'));
    }

    public function update(Request $request)
    {
        $accountId = $this->requireAccountId();

        if (!auth()->user()->hasPermissionInAccount($accountId, 'settings.manage')) {
            abort(403, 'You do not have permission to update settings.');
        }

        $account = Account::findOrFail($accountId);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency_code' => ['required', 'string', 'in:IQD,USD'],
        ]);

        $account->update([
            'name' => $request->name,
            'currency_code' => $request->currency_code,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
