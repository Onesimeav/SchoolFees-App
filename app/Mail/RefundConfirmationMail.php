<?php

namespace App\Mail;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RefundConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly RefundRequest $refundRequest,
        public readonly string $receiptPath,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Remboursement confirmé — ' . ($this->refundRequest->transaction?->fee?->title ?? 'Frais'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.refund-confirmation',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => Storage::disk('supabase')->get($this->receiptPath),
                'recu-remboursement.pdf',
            )->withMime('application/pdf'),
        ];
    }
}