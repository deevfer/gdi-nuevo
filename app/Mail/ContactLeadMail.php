<?php

namespace App\Mail;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use App\Services\EmailTexts;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Avisa al equipo de un nuevo mensaje; al responder, la respuesta va directo a la persona. */
class ContactLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contact) {}

    public function envelope(): Envelope
    {
        $copy = app(EmailTexts::class)->get('lead');

        return new Envelope(
            subject: $copy['subject'].' · '.($this->contact->subject ?: $this->contact->name),
            replyTo: [new Address($this->contact->email, $this->contact->name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-lead',
            with: [
                'copy' => app(EmailTexts::class)->get('lead'),
                'inboxUrl' => $this->contact->exists
                    ? ContactMessageResource::getUrl('view', ['record' => $this->contact], panel: 'admin')
                    : ContactMessageResource::getUrl(panel: 'admin'),
            ],
        );
    }
}
