<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWalletRequest;
use App\Models\Wallet;
use App\Services\AccountContext;
use Illuminate\Http\Request;

/**
 * WalletController (API)
 * 
 * API endpoints for wallet management.
 * IMPORTANT: This controller only uses services - no web-specific logic.
 */
class WalletController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index()
    {
        $accountId = $this->accountContext->id();
        $wallets = Wallet::forAccount($accountId)->get();
        return response()->json($wallets);
    }

    public function store(StoreWalletRequest $request)
    {
        $data = $request->validated();
        $data['account_id'] = $this->accountContext->id();
        $wallet = Wallet::create($data);
        return response()->json($wallet, 201);
    }

    public function show(Wallet $wallet)
    {
        $this->authorize('view', $wallet);
        return response()->json($wallet);
    }

    public function update(StoreWalletRequest $request, Wallet $wallet)
    {
        $this->authorize('update', $wallet);
        $wallet->update($request->validated());
        return response()->json($wallet);
    }

    public function destroy(Request $request, Wallet $wallet)
    {
        $this->authorize('delete', $wallet);
        $wallet->update(['deleted_by' => $request->user()->id]);
        $wallet->delete();
        return response()->json(null, 204);
    }
}
