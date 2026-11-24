<?php

namespace App\Http\Controllers;

use App\Mail\ContactConfirmationMail;
use App\Mail\ContactLeadMail;
use App\Models\ContactMessage;
use Illuminate\Http\Response;

/** Muestra los correos con datos de ejemplo para revisar sus textos desde el CMS. */
class EmailPreviewController extends Controller
{
    public function __invoke(string $mail): Response
    {
        $contact = (new ContactMessage)->forceFill([
            'id' => 128,
            'name' => 'Ana López',
            'email' => 'ana.lopez@empresa.com',
            'company' => 'Empresa de ejemplo, S.A.',
            'phone' => '+502 5555-1234',
            'subject' => 'Estrategias de inversión',
            'message' => "Quisiera conocer las condiciones de acceso al GDI Income & Liquidity Fund en USD.\n¿Podemos agendar una llamada esta semana?",
            'created_at' => now(),
        ]);

        $mailable = match ($mail) {
            'aviso-al-equipo' => new ContactLeadMail($contact),
            'confirmacion' => new ContactConfirmationMail($contact),
            default => abort(404),
        };

        return response($mailable->render());
    }
}
