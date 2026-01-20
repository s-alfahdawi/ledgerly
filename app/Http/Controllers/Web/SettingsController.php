<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AccountContext;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index()
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        
        // Check if user has permission to view settings
        if (!auth()->user()->hasPermissionInAccount($accountId, 'settings.view')) {
            abort(403, 'You do not have permission to view settings.');
        }
        
        $account = \App\Models\Account::findOrFail($accountId);

        return view('settings.index', compact('account'));
    }

    public function update(Request $request)
    {
        $accountId = $this->accountContext->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        
        // Check if user has permission to manage settings
        if (!auth()->user()->hasPermissionInAccount($accountId, 'settings.manage')) {
            abort(403, 'You do not have permission to update settings.');
        }
        
        $account = \App\Models\Account::findOrFail($accountId);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency_code' => ['required', 'string', 'in:IQD,USD'],
        ]);

        $account->update([
            'name' => $request->name,
            'currency_code' => $request->currency_code,
            // timezone is not updated - always Asia/Baghdad
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
