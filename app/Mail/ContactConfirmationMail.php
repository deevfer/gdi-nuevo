<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Services\EmailTexts;
use App\Services\SiteSeo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Confirma a la persona que su mensaje llegó; al responder, la respuesta va al equipo. */
class ContactConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contact) {}

    public function envelope(): Envelope
    {
        $settings = SiteSetting::current();
        $replyTo = $settings->mailRecipients()[0] ?? $settings->mail_from_address;

        return new Envelope(
            subject: app(EmailTexts::class)->get('confirmation')['subject'],
            replyTo: filled($replyTo) ? [new Address($replyTo, $settings->mail_from_name ?: 'GDI Valores')] : [],
        );
    }

    public function content(): Content
    {
        $copy = app(EmailTexts::class)->get('confirmation');
        $href = trim((string) $copy['cta_url']);

        return new Content(
            view: 'emails.contact-confirmation',
            with: [
                'copy' => $copy,
                'ctaUrl' => $href === '' ? null : (preg_match('~^https?://~i', $href) ? $href : app(SiteSeo::class)->absolute($href)),
            ],
        );
    }
}
