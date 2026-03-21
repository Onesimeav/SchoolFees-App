<?php

namespace App\Mail;

use App\Models\Fee;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GeneralFeeReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Transaction $transaction,
        public readonly Fee $fee,
        public readonly User $user,
        public readonly string $receiptPath,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reçu — ' . $this->fee->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.general-fee-receipt',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => Storage::disk('supabase')->get($this->receiptPath),
                'recu-frais-general.pdf',
            )->withMime('application/pdf'),
        ];
    }
}