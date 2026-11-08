<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Student $student;
    public string $plainPassword;
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, string $plainPassword, ?string $loginUrl = null)
    {
        $this->student = $student;
        $this->plainPassword = $plainPassword;
        $this->loginUrl = $loginUrl ?: route('login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ការអនុម័តចុះឈ្មោះចូលរៀនជោគជ័យ - គណនីប្រព័ន្ធសាលារៀន PDT',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-approved',
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

