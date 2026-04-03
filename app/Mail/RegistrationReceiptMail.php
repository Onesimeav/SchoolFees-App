<?php

namespace App\Mail;

use App\Models\ClassRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RegistrationReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ClassRegistration $registration,
        public readonly string $receiptPath,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reçu d\'inscription — ' . $this->registration->grade->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-receipt',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => Storage::disk('supabase')->get($this->receiptPath),
                'recu-inscription.pdf',
            )->withMime('application/pdf'),
        ];
    }
}