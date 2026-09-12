<?php

namespace App\Mail;

use App\Models\BorrowingTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverdueNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BorrowingTransaction $transaction
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[UB EBS] Warning: Overdue Borrowed Equipment (Penalty Notice)',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.overdue_notice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
