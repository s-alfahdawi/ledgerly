<?php

namespace App\Http\Controllers;

use App\Services\AccountContext;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    protected function requireAccountId(): int
    {
        $accountId = app(AccountContext::class)->id();
        if (!$accountId) {
            abort(403, 'No account selected.');
        }
        return $accountId;
    }
}
