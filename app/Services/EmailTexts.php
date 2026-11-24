<?php

namespace App\Services;

use App\Models\PageSection;

/**
 * Textos de los correos, editables en Configuración › Textos de correos.
 * Los campos vacíos usan los textos por defecto.
 */
class EmailTexts
{
    public const DEFAULTS = [
        'lead' => [
            'subject' => 'Nuevo mensaje desde el sitio',
            'eyebrow' => 'FORMULARIO DE CONTACTO',
            'title' => 'Nuevo mensaje',
            'intro' => 'escribió desde el formulario de contacto del sitio.',
            'cta' => 'Ver en el CMS',
            'reply_note' => 'Puede responder directamente a este correo: la respuesta llegará a la persona que escribió.',
            'footer' => 'Aviso automático del sitio web.',
        ],
        'confirmation' => [
            'enabled' => true,
            'subject' => 'Recibimos su mensaje · GDI Valores',
            'preheader' => 'Gracias por escribirnos. Nuestro equipo le responderá a la brevedad.',
            'eyebrow' => 'CONFIRMACIÓN',
            'title' => 'Recibimos su mensaje',
            'greeting' => 'Hola',
            'body' => 'Gracias por comunicarse con GDI Valores. Nuestro equipo revisará su consulta y le responderá a la brevedad.',
            'summary_title' => 'Resumen de su mensaje',
            'reply_note' => 'Si necesita agregar información, puede responder directamente a este correo.',
            'cta' => 'Conocer nuestras estrategias',
            'cta_url' => '/estrategias-de-inversion',
            'signature' => "Atentamente,\nEquipo GDI Valores",
            'footer' => 'Recibe este correo porque escribió desde el formulario de contacto de nuestro sitio.',
        ],
    ];

    /** @return array<string, mixed> */
    public function get(string $email): array
    {
        $saved = PageSection::query()->where('page', 'emails')->where('section', $email)->value('content') ?? [];

        return array_replace(self::DEFAULTS[$email], array_filter($saved, fn (mixed $value): bool => $value !== null && $value !== ''));
    }
}
