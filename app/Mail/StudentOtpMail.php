<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $otpCode,
        public int $expiryMinutes = 15
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'UB EBS - Account Verification OTP Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp_code',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
