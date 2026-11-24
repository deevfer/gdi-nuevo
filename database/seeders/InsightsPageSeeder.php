<?php

namespace Database\Seeders;

use App\Models\Insight;
use App\Models\PageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class InsightsPageSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'hero' => [
                'eyebrow' => 'ANÁLISIS Y PERSPECTIVAS',
                'title' => 'Insights',
                'description' => 'Análisis técnico, perspectivas de mercado y reflexiones sobre administración de capital, estructuración financiera y mercado de valores.',
                'image_desktop' => null,
                'image_mobile' => null,
            ],

            'listing' => [
                'featured_label' => 'DESTACADO',
                'read_more' => 'Leer más',
                'empty_text' => 'Todavía no hay artículos en esta categoría.',
            ],

            'categories' => [
                'eyebrow' => 'EXPLORAR',
                'title' => 'Categorías',
                'items' => [
                    ['name' => 'Estructuración'],
                    ['name' => 'Inversión'],
                    ['name' => 'Gobierno'],
                    ['name' => 'Análisis'],
                    ['name' => 'Estrategia'],
                    ['name' => 'Regulación'],
                ],
            ],

            'subscribe' => [
                'eyebrow' => 'SUSCRIPCIÓN',
                'title' => 'Reciba nuestros insights',
                'description' => 'Suscríbase para recibir análisis técnico y perspectivas sobre mercado de capitales, administración de activos y estructuración financiera.',
                'button_text' => 'Suscribirse',
                'button_url' => '/contacto?asunto=Suscripci%C3%B3n%20a%20Insights',
            ],

            'seo' => [
                'meta_title' => 'Insights | GDI Valores',
                'meta_description' => 'Análisis técnico, perspectivas de mercado y reflexiones sobre administración de capital, estructuración financiera y mercado de valores.',
                'canonical_url' => '',
                'og_title' => 'Insights | GDI Valores',
                'og_description' => 'Análisis y perspectivas de GDI Valores sobre mercado de capitales y administración de activos.',
                'og_image' => null,
            ],
        ];

        // No sobrescribir cambios posteriores hechos desde Filament.
        foreach ($sections as $section => $content) {
            PageSection::firstOrCreate(
                ['page' => 'insights', 'section' => $section],
                ['content' => $content],
            );
        }

        // Artículos del diseño. El cuerpo completo lo redacta el equipo desde el CMS.
        $articles = [
            ['mercado-de-capitales-financiamiento-corporativo', 'Mercado de capitales como alternativa de financiamiento corporativo', 'Estructuración', '2026-03-01', 'Análisis de las ventajas estructurales del mercado de capitales frente a financiamiento bancario tradicional.', 'articulo-mercado-capitales.png', true],
            ['gestion-disciplinada-de-liquidez', 'Gestión disciplinada de liquidez en entornos de tasas cambiantes', 'Inversión', '2026-02-01', 'Consideraciones técnicas para la administración de carteras de renta fija en ciclos de política monetaria.', 'articulo-liquidez.jpg', false],
            ['gobierno-corporativo-en-vehiculos-de-inversion', 'Marcos de gobierno corporativo en vehículos de inversión', 'Gobierno', '2026-01-01', 'Estructura de comités, procesos de decisión y controles en la administración institucional de capital.', 'articulo-gobierno.jpg', false],
            ['due-diligence-en-emisiones-de-deuda-corporativa', 'Due diligence en emisiones de deuda corporativa', 'Análisis', '2025-12-01', 'Proceso de evaluación técnica, análisis crediticio y estructuración de garantías en emisiones corporativas.', 'articulo-due-diligence.jpg', false],
            ['diversificacion-institucional-multi-moneda', 'Diversificación institucional: estrategias multi-moneda', 'Estrategia', '2025-11-01', 'Construcción de carteras institucionales con exposición a USD y moneda local bajo criterios de diversificación.', 'articulo-multimoneda.jpg', false],
            ['marcos-regulatorios-mercado-de-valores-guatemalteco', 'Marcos regulatorios en el mercado de valores guatemalteco', 'Regulación', '2025-10-01', 'Revisión de requerimientos regulatorios, supervisión y estándares aplicables a casas de valores y fondos de inversión.', 'articulo-regulacion.jpg', false],
        ];

        foreach ($articles as [$slug, $title, $category, $date, $excerpt, $image, $featured]) {
            // Se copian al disco public para que el CMS las trate como imágenes subidas.
            $path = 'gdi/insights/'.$image;
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents(public_path('images/gdi/insights/'.$image)));
            }

            Insight::firstOrCreate(['slug' => $slug], [
                'title' => $title,
                'category' => $category,
                'excerpt' => $excerpt,
                'body' => '<p>'.e($excerpt).'</p>',
                'image' => $path,
                'is_featured' => $featured,
                'is_published' => true,
                'published_at' => $date,
            ]);
        }
    }
}
