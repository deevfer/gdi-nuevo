<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [

            // SECCIÓN 1 — HERO
            'hero' => [
                'title' => 'Plataforma institucional regulada para administrar capital y estructurar financiamiento',
                'description' => 'GDI Valores articula análisis, estructuración y ejecución dentro de un mismo marco institucional, permitiendo a inversionistas y empresas acceder al mercado de capitales con mayor disciplina, control y capacidad de ejecución.',
                'image_desktop' => null,
                'image_mobile' => null,
                'button_primary_text' => 'Para inversionistas',
                'button_primary_url' => '/inversionistas',
                'button_secondary_text' => 'Para empresas',
                'button_secondary_url' => '/financiamiento-estructurado',
            ],

            // SECCIÓN 2 — INFRAESTRUCTURA
            'infrastructure' => [
                'eyebrow' => 'INFRAESTRUCTURA',
                'title' => 'Una estructura integrada para ejecutar con mayor rigor',
                'description' => 'Cuando análisis, asignación, estructuración y ejecución operan por separado, aumenta la fricción y se diluye el control. GDI integra estas funciones dentro de una infraestructura regulada, reduciendo desalineaciones y elevando la calidad de implementación.',
                'diagram' => [
                    'capital_title' => 'CAPITAL',
                    'capital_items' => [
                        'Institucional',
                        'Calificado',
                        'Corporativo',
                    ],
                    'center_title' => 'Plataforma de mercado de capitales',
                    'center_items' => [
                        'Análisis y asignación',
                        'Estructuración',
                        'Ejecución',
                    ],
                    'solutions_title' => 'SOLUCIONES',
                    'solutions_items' => [
                        'Estrategias de inversión',
                        'Financiamiento estructurado',
                        'Acceso a mercado de capitales',
                    ],
                    'footer_title' => 'Infraestructura integrada:',
                    'footer_description' => 'GDI no actúa como intermediario sino como plataforma que controla análisis, estructuración y ejecución bajo un mismo marco institucional. El capital fluye mediante procesos disciplinados que operan dentro del mercado de valores regulado, eliminando fragmentación entre funciones.',
                ],
            ],

            // SECCIÓN 3 — MODELO OPERATIVO
            'operating_model' => [
                'eyebrow' => 'MODELO OPERATIVO',
                'title' => 'Un modelo que reduce fricción y sincroniza recursos con ejecución',
                'description' => 'Al integrar administración de recursos financieros, estructuración y ejecución dentro de una misma plataforma, eliminamos fragmentación, reducimos costos de coordinación y aceleramos la materialización de oportunidades.',
                'cards' => [
                    [
                        'number' => '01',
                        'title' => 'Alineación de capital',
                        'description' => 'El capital se asigna bajo criterios técnicos dentro de un mismo marco institucional, evitando desalineaciones entre inversionistas y oportunidades.',
                    ],
                    [
                        'number' => '02',
                        'title' => 'Eficiencia en estructuración',
                        'description' => 'Las oportunidades se diseñan desde su origen para ser ejecutables, reduciendo iteraciones y fricción entre originación y financiamiento.',
                    ],
                    [
                        'number' => '03',
                        'title' => 'Ejecución integrada',
                        'description' => 'Capacidad de ejecutar tanto en mercado público de valores bajo una infraestructura regulada como en el mercado privado, reduciendo dependencia de terceros.',
                    ],
                ],
            ],

            // SECCIÓN 4 — ACCESO DIFERENCIADO
            'audiences' => [
                'eyebrow' => 'ACCESO DIFERENCIADO',
                'title' => 'Elija el camino que corresponde a su perfil',
                'description' => 'GDI articula dos capacidades dentro de una misma infraestructura institucional: administración de capital para inversionistas y estructuración de financiamiento para empresas.',
                'cards' => [
                    [
                        'title' => 'Inversionistas',
                        'description' => 'Dirigido a inversionistas patrimoniales, institucionales, tesorerías corporativas y actores que buscan acceso disciplinado al mercado de capitales.',
                        'secondary_description' => 'Acceso a estrategias administradas bajo criterios institucionales de liquidez, riesgo, gobierno y ejecución, dentro de una infraestructura regulada.',
                        'image' => null,
                        'button_text' => 'Ver soluciones para inversionistas',
                        'button_url' => '/inversionistas',
                    ],
                    [
                        'title' => 'Empresas',
                        'description' => 'Dirigido a compañías que requieren diversificar sus fuentes de capital y estructurar financiamiento más allá del crédito bancario tradicional.',
                        'secondary_description' => 'Diseño, estructuración y ejecución de soluciones de financiamiento en mercado de capitales, alineadas con el perfil corporativo y la capacidad de implementación.',
                        'image' => null,
                        'button_text' => 'Ver soluciones para financiamiento',
                        'button_url' => '/financiamiento-estructurado',
                    ],
                ],
            ],

            // SECCIÓN 5 — ACCESO A LA PLATAFORMA
            'platform_access' => [
                'eyebrow' => 'SIGUIENTE PASO',
                'title' => 'Acceso a la plataforma',
                'description' => 'El proceso de onboarding varía según perfil de inversionista. Contacte al equipo para evaluar estructura de acceso adecuada y requerimientos aplicables.',
                'button_text' => 'Contactar equipo',
                'button_url' => '/contacto',
            ],

            // SEO — INICIO
            'seo' => [
                'meta_title' => 'GDI Valores | Plataforma institucional de capitales',
                'meta_description' => 'GDI Valores integra administración de capital, estructuración y ejecución dentro de una infraestructura institucional regulada.',
                'og_title' => 'GDI Valores | Plataforma institucional de capitales',
                'og_description' => 'Conozca la plataforma institucional de GDI Valores para inversionistas y empresas.',
                'og_image' => null,
            ],
        ];

        foreach ($sections as $section => $content) {
            PageSection::firstOrCreate(
                [
                    'page' => 'home',
                    'section' => $section,
                ],
                [
                    'content' => $content,
                ]
            );
        }
    }
}
