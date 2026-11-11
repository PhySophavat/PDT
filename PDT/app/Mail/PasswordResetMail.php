<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $recipientName;
    public string $resetUrl;
    public string $otpCode;
    public int $expireMinutes;
    public string $schoolName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $recipientName, string $resetUrl, string $otpCode, int $expireMinutes = 3, string $schoolName = 'សាលារៀន ជំនាន់ថ្មី')
    {
        $this->recipientName = $recipientName;
        $this->resetUrl = $resetUrl;
        $this->otpCode = $otpCode;
        $this->expireMinutes = $expireMinutes;
        $this->schoolName = $schoolName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔒 លេខកូដ OTP ({$this->otpCode}) សម្រាប់កំណត់ពាក្យសម្ងាត់ថ្មី - {$this->schoolName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
