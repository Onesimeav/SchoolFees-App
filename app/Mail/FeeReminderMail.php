<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeeReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User    $user,
        public readonly string  $feeTitle,
        public readonly string  $academicYear,
        public readonly string  $gradeName,
        public readonly float   $amount,
        public readonly string  $dueDate,
        public readonly string  $type,              // 'near_due' | 'past_due'
        public readonly ?int    $installmentNumber = null,
        public readonly ?string $portalUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->type === 'near_due'
            ? 'Rappel : échéance dans 7 jours — ' . $this->feeTitle
            : 'Frais en retard : ' . $this->feeTitle;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.fee-reminder');
    }

    public function attachments(): array
    {
        return [];
    }
}