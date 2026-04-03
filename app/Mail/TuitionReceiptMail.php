<?php

namespace App\Mail;

use App\Models\Fee;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class TuitionReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Collection $transactions,
        public readonly Fee $fee,
        public readonly User $user,
        public readonly string $receiptPath,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reçu de scolarité — ' . ($this->fee->grade?->name ?? $this->fee->title),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tuition-receipt',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => Storage::disk('supabase')->get($this->receiptPath),
                'recu-scolarite.pdf',
            )->withMime('application/pdf'),
        ];
    }
}