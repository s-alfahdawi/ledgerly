<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteMemberRequest;
use App\Mail\AccountInvitationMail;
use App\Models\Account;
use App\Models\AccountInvitation;
use App\Models\User;
use App\Services\AccountContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $accountId = $this->requireAccountId();
        $account = Account::findOrFail($accountId);
        $members = $account->users()->withPivot('role', 'joined_at')->get();

        return view('members.index', compact('members', 'account'));
    }

    public function store(InviteMemberRequest $request)
    {
        $accountId = $this->requireAccountId();
        $account = Account::findOrFail($accountId);

        // Check if user has permission to invite members
        $user = $request->user();
        $membership = $user->accounts()->where('accounts.id', $accountId)->first();
        if (!$membership || !in_array($membership->pivot->role, ['owner', 'admin'])) {
            abort(403, 'You do not have permission to invite members.');
        }

        $invitedUser = User::where('email', $request->email)->first();

        if ($invitedUser) {
            // User already exists, add them directly
            if (!$account->users()->where('users.id', $invitedUser->id)->exists()) {
                $account->users()->attach($invitedUser->id, [
                    'role' => $request->role,
                    'invited_by' => $user->id,
                    'joined_at' => now(),
                ]);
                return redirect()->route('members.index')
                    ->with('success', 'Member invited successfully.');
            } else {
                return redirect()->route('members.index')
                    ->with('error', 'User is already a member of this account.');
            }
        } else {
            // User doesn't exist, create invitation
            $invitation = AccountInvitation::createInvitation(
                $accountId,
                $request->email,
                $request->role,
                $user->id
            );

            // Send invitation email
            try {
                Mail::to($request->email)->send(
                    new AccountInvitationMail($invitation)
                );

                $mailDriver = config('mail.default');
                if ($mailDriver === 'log') {
                    return redirect()->route('members.index')
                        ->with('success', 'Invitation created successfully. Email has been logged (check storage/logs/laravel.log). To send real emails, configure SMTP in your .env file.');
                }

                return redirect()->route('members.index')
                    ->with('success', 'Invitation sent successfully. The user will receive an email with a registration link.');
            } catch (\Exception $e) {
                Log::error('Failed to send invitation email', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $errorMessage = 'Invitation created but email failed to send. ';
                if (str_contains($e->getMessage(), 'authentication')) {
                    $errorMessage .= 'SMTP authentication failed. Please check your MAIL_USERNAME and MAIL_PASSWORD in .env. ';
                    $errorMessage .= 'Note: Port 465 requires MAIL_ENCRYPTION=ssl (not tls). Port 587 uses MAIL_ENCRYPTION=tls.';
                } else {
                    $errorMessage .= 'Error: ' . $e->getMessage();
                }
                return redirect()->route('members.index')
                    ->with('error', $errorMessage);
            }
        }
    }

    public function update(Request $request, User $member)
    {
        $this->authorize('update', $member);

        $accountId = $this->requireAccountId();

        $request->validate([
            'role' => ['required', 'in:admin,member,viewer'],
        ]);

        $account = Account::findOrFail($accountId);

        $account->users()->updateExistingPivot($member->id, [
            'role' => $request->role,
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Member role updated successfully.');
    }

    public function destroy(User $member)
    {
        $this->authorize('delete', $member);

        $accountId = $this->requireAccountId();
        $account = Account::findOrFail($accountId);

        // Prevent removing the account owner
        if ($account->owner_user_id === $member->id) {
            return redirect()->route('members.index')
                ->with('error', 'The account owner cannot be removed.');
        }

        $account->users()->detach($member->id);

        return redirect()->route('members.index')
            ->with('success', 'Member removed successfully.');
    }
}
