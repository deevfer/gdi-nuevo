<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class ContactPageSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'hero' => [
                'eyebrow' => 'COMUNICACIÓN',
                'title' => 'Contacto',
                'description' => 'Comuníquese con nuestro equipo para información sobre estrategias de inversión, soluciones de financiamiento o consultas institucionales.',
                'image_desktop' => null,
                'image_mobile' => null,
            ],

            'form' => [
                'eyebrow' => 'FORMULARIO',
                'title' => 'Envíenos un mensaje',
                'submit_text' => 'Enviar mensaje',
                'success_message' => 'Gracias por escribirnos. Nuestro equipo le responderá a la brevedad.',
                'subjects' => [
                    ['name' => 'Estrategias de inversión'],
                    ['name' => 'Financiamiento estructurado'],
                    ['name' => 'Suscripción a Insights'],
                    ['name' => 'Consulta general'],
                ],
            ],

            'info' => [
                'eyebrow' => 'INFORMACIÓN',
                'title' => 'Información de contacto',
                'office_title' => 'Oficinas',
                'office_text' => 'Boulevard Vista Hermosa Zona 15, Vista Hermosa II, Edificio Avante, Oficina 904, Ciudad de Guatemala, Guatemala',
                'phone_title' => 'Teléfono',
                'phone' => '+502 2233-5300',
                'email_title' => 'Email',
                'email' => 'info@gdivalores.com',
                'hours_title' => 'Horario de atención',
                'hours_text' => 'Lunes a viernes 8:00 AM - 5:00 PM',
            ],

            'areas' => [
                'eyebrow' => 'ESPECIALIZACIÓN',
                'title' => 'Áreas de contacto',
                'cards' => [
                    ['title' => 'Inversionistas', 'description' => 'Información sobre estrategias de inversión, fondos y proceso de asignación de capital.', 'email' => 'inversiones@gdivalores.com'],
                    ['title' => 'Empresas emisoras', 'description' => 'Consultas sobre soluciones de financiamiento, estructuración y acceso al mercado de capitales.', 'email' => 'estructuracion@gdivalores.com'],
                    ['title' => 'Consultas generales', 'description' => 'Información institucional, prensa y otras consultas.', 'email' => 'info@gdivalores.com'],
                ],
            ],

            'legal' => [
                'title' => 'Aviso legal',
                'text' => 'GDI Valores es una casa de valores registrada ante el Registro del Mercado de Valores y Mercancías (Ministerio de Economía) y la Bolsa de Valores Nacional de Guatemala, con supervisión en materia de prevención de lavado de dinero por la Intendencia de Verificación Especial (IVE). La información contenida en este sitio web tiene carácter informativo y no constituye una oferta de venta ni una solicitud de compra de valores. Las inversiones implican riesgos y los rendimientos pasados no garantizan resultados futuros.',
            ],

            'seo' => [
                'meta_title' => 'Contacto | GDI Valores',
                'meta_description' => 'Comuníquese con GDI Valores para información sobre estrategias de inversión, soluciones de financiamiento o consultas institucionales.',
                'canonical_url' => '',
                'og_title' => 'Contacto | GDI Valores',
                'og_description' => 'Contacte al equipo de GDI Valores en Ciudad de Guatemala.',
                'og_image' => null,
            ],
        ];

        // No sobrescribir cambios posteriores hechos desde Filament.
        foreach ($sections as $section => $content) {
            PageSection::firstOrCreate(
                ['page' => 'contact', 'section' => $section],
                ['content' => $content],
            );
        }
    }
}
