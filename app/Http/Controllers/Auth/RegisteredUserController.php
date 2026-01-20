<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Account;
use App\Services\AccountContext;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $invitation = null;
        if ($request->has('token')) {
            $invitation = \App\Models\AccountInvitation::where('token', $request->token)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();
        }
        return view('auth.register', compact('invitation'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
        
        // If there's an invitation token, currency is not required
        if (!$request->has('invitation_token')) {
            $rules['currency_code'] = ['required', 'string', 'in:IQD,USD'];
        }
        
        $request->validate($rules);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Check if there's a pending invitation
            $invitation = null;
            if ($request->has('invitation_token')) {
                $invitation = \App\Models\AccountInvitation::where('token', $request->invitation_token)
                    ->where('email', $request->email)
                    ->whereNull('accepted_at')
                    ->where('expires_at', '>', now())
                    ->first();
            }

            if ($invitation) {
                // Accept invitation and join existing account
                $account = $invitation->account;
                $account->users()->attach($user->id, [
                    'role' => $invitation->role,
                    'invited_by' => $invitation->invited_by,
                    'joined_at' => now(),
                ]);
                
                $invitation->update(['accepted_at' => now()]);
            } else {
                // Auto-create a default account for the user
                $account = Account::create([
                    'name' => $user->name . "'s Account",
                    'currency_code' => $request->currency_code ?? 'IQD',
                    'timezone' => 'Asia/Baghdad', // Default timezone
                    'owner_user_id' => $user->id,
                ]);

                // Add user to the account as owner
                $account->users()->attach($user->id, [
                    'role' => 'owner',
                    'joined_at' => now(),
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        // Set the account context
        $account = $user->accounts()->first();
        if ($account) {
            $this->accountContext->set($account->id);
        }
        
        // If user registered via invitation, show a success message
        if ($request->has('invitation_token')) {
            return redirect(route('dashboard', absolute: false))
                ->with('success', 'Welcome! You have successfully joined ' . $account->name . ' as a ' . ucfirst($user->accounts()->first()->pivot->role) . '.');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
