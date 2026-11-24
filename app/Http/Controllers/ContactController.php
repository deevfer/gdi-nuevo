<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactConfirmationMail;
use App\Mail\ContactLeadMail;
use App\Models\ContactMessage;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Services\EmailTexts;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::create([
            ...$request->safe()->except(['website', 'g-recaptcha-response']),
            'ip_address' => $request->ip(),
        ]);

        $this->notify($message, SiteSetting::current());

        $form = PageSection::query()
            ->where('page', 'contact')
            ->where('section', 'form')
            ->value('content') ?? [];

        // El id permite a los píxeles (GA4, Ads, Meta, LinkedIn) registrar el lead una sola vez.
        return response()->json([
            'id' => $message->id,
            'message' => $form['success_message'] ?? 'Gracias por escribirnos. Le responderemos a la brevedad.',
        ], 201);
    }

    /**
     * Aviso al equipo y confirmación a la persona, con la cuenta de Configuración › Configuración del sitio
     * y los textos de Configuración › Textos de correos. Un fallo de correo no debe perder el mensaje guardado.
     */
    private function notify(ContactMessage $message, SiteSetting $settings): void
    {
        if (! $settings->canSendMail()) {
            return;
        }

        try {
            $settings->applyMailConfiguration();

            if ($settings->mailRecipients() !== []) {
                Mail::to($settings->mailRecipients())->send(new ContactLeadMail($message));
            }

            if (app(EmailTexts::class)->get('confirmation')['enabled']) {
                Mail::to($message->email, $message->name)->send(new ContactConfirmationMail($message));
            }
        } catch (Throwable $exception) {
            Log::warning('No se pudo enviar el correo de contacto.', [
                'contact_message_id' => $message->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
