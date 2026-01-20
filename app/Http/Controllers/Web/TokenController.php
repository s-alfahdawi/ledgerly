<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    public function index()
    {
        $tokens = auth()->user()->tokens()->get();

        return view('tokens.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['nullable', 'array'],
        ]);

        $abilities = $request->abilities ?? ['transactions:read', 'transactions:write'];

        $token = auth()->user()->createToken($request->name, $abilities);

        return redirect()->route('tokens.index')
            ->with('success', 'Token created successfully. Please copy it now: ' . $token->plainTextToken);
    }

    public function destroy($tokenId)
    {
        auth()->user()->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('tokens.index')
            ->with('success', 'Token revoked successfully.');
    }
}
