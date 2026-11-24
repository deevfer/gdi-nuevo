<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

/** Menú, pie de página y aviso de cookies: mismos textos que el sitio mostraba antes de ser editables. */
class GlobalContentSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'header' => [
                'brand_name' => 'GDI Valores',
                'links' => [
                    ['label' => 'Inicio', 'url' => '/'],
                    ['label' => 'Plataforma', 'url' => '/plataforma'],
                    ['label' => 'Financiamiento Estructurado', 'url' => '/financiamiento-estructurado'],
                    ['label' => 'Inversionistas', 'url' => '/inversionistas'],
                    ['label' => 'Estrategias de Inversión', 'url' => '/estrategias-de-inversion'],
                    ['label' => 'Insights', 'url' => '/insights'],
                    ['label' => 'Contacto', 'url' => '/contacto'],
                ],
            ],
            'footer' => [
                'brand_name' => 'GDI Valores',
                'description' => 'Plataforma institucional regulada para la administración de capital y estructuración de financiamiento.',
                'groups' => [
                    ['title' => 'Plataforma', 'links' => [
                        ['label' => 'Acerca de GDI', 'url' => '/plataforma'],
                        ['label' => 'Para Inversionistas', 'url' => '/inversionistas'],
                        ['label' => 'Contacto', 'url' => '/contacto'],
                    ]],
                    ['title' => 'Servicios', 'links' => [
                        ['label' => 'Estrategias de Inversión', 'url' => '/estrategias-de-inversion'],
                        ['label' => 'Capital Solutions', 'url' => '/financiamiento-estructurado'],
                    ]],
                    ['title' => 'Recursos', 'links' => [
                        ['label' => 'Insights', 'url' => '/insights'],
                        ['label' => 'Contacto', 'url' => '/contacto'],
                    ]],
                ],
                'copyright' => '© {año} GDI Valores. Todos los derechos reservados.',
                'bottom_note' => 'Miembro del ecosistema Aura Capital',
            ],
            'cookies' => [
                'trigger' => 'Cookies',
                'title' => 'Usamos cookies',
                'body' => 'Usamos cookies esenciales para que el sitio funcione y, con su permiso, cookies de analítica y marketing para medir visitas y mejorar nuestras campañas.',
                'essential_title' => 'Esenciales',
                'essential_body' => 'Necesarias para el funcionamiento del sitio. Siempre activas.',
                'analytics_title' => 'Analítica',
                'analytics_body' => 'Nos ayudan a entender cómo se usa el sitio.',
                'marketing_title' => 'Marketing',
                'marketing_body' => 'Permiten medir campañas en Google, Meta y LinkedIn.',
                'accept' => 'Aceptar todas',
                'reject' => 'Solo esenciales',
                'configure' => 'Configurar',
                'save' => 'Guardar preferencias',
            ],
        ];

        // No sobrescribir cambios posteriores hechos desde Filament.
        foreach ($sections as $section => $content) {
            PageSection::firstOrCreate(['page' => 'global', 'section' => $section], ['content' => $content]);
        }
    }
}
