<?php

namespace App\Mail;

use App\Models\Account;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowBalanceAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Account $account,
        public array $lowWallets,
        public string $currencyCode,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Low Balance Alert — ' . $this->account->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-balance-alert',
        );
    }
}
