<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class FinancingPageSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'hero' => [
                'eyebrow' => 'FINANCIAMIENTO ESTRUCTURADO',
                'title' => 'Estructuración de financiamiento para empresas que requieren más que crédito bancario',
                'description' => 'GDI diseña, documenta y ejecuta emisiones públicas y privadas para compañías que buscan diversificar sus fuentes de financiamiento con estructuras viables, regulatoriamente ejecutables y alineadas con su perfil corporativo.',
                'image_desktop' => null,
                'image_mobile' => null,
            ],

            'problem_solution' => [
                'problem_eyebrow' => 'PROBLEMA',
                'problem_title' => 'El problema',
                'problem_paragraph_one' => 'Las empresas que dependen exclusivamente de financiamiento bancario tradicional enfrentan limitaciones en términos de plazos, montos y estructuras.',
                'problem_paragraph_two' => 'La concentración en una sola fuente de financiamiento reduce flexibilidad estratégica y puede limitar el crecimiento.',
                'solution_eyebrow' => 'SOLUCIÓN',
                'solution_title' => 'La solución',
                'solution_paragraph_one' => 'El mercado de capitales permite acceder a financiamiento estructurado mediante emisiones de deuda pública o privada, diversificando fuentes y optimizando condiciones.',
                'solution_paragraph_two' => 'GDI Valores estructura, documenta y ejecuta emisiones bajo estándares institucionales, conectando empresas con capital institucional.',
            ],

            'companies' => [
                'eyebrow' => 'EMPRESAS',
                'kicker' => 'COMPAÑÍAS MEDIANAS Y GRANDES',
                'title' => 'Estructuramos capital para empresas con planes de crecimiento, refinanciamiento y expansión',
                'button_text' => 'HABLAR CON EL EQUIPO',
                'button_url' => '/contacto',
                'background_image' => null,
            ],

            'process' => [
                'eyebrow' => 'METODOLOGÍA',
                'title' => 'Proceso de estructuración',
                'description' => 'Tres etapas integradas para transformar necesidades de capital en estructuras ejecutables dentro del mercado regulado.',
                'steps' => [
                    [
                        'number' => '01',
                        'title' => 'Diagnóstico',
                        'description' => 'Evaluación de la situación financiera de la empresa, necesidades de capital, estructura de balance y capacidad de servicio de deuda.',
                        'items' => [
                            ['text' => 'Análisis financiero detallado'],
                            ['text' => 'Evaluación de proyecciones'],
                            ['text' => 'Revisión de gobierno corporativo'],
                        ],
                    ],
                    [
                        'number' => '02',
                        'title' => 'Estructuración',
                        'description' => 'Diseño de la estructura de financiamiento, definición de términos, garantías, covenants y documentación legal.',
                        'items' => [
                            ['text' => 'Diseño de estructura financiera'],
                            ['text' => 'Preparación de documentación'],
                            ['text' => 'Proceso regulatorio'],
                        ],
                    ],
                    [
                        'number' => '03',
                        'title' => 'Colocación',
                        'description' => 'Ejecución de la emisión a través de colocación pública o privada, marketing con inversionistas institucionales y cierre de operación.',
                        'items' => [
                            ['text' => 'Roadshow con inversionistas'],
                            ['text' => 'Proceso de bookbuilding'],
                            ['text' => 'Cierre y desembolso'],
                        ],
                    ],
                ],
            ],

            'instruments' => [
                'eyebrow' => 'INSTRUMENTOS',
                'title' => 'Tipos de emisiones',
                'description' => 'Vías reguladas para canalizar capital institucional hacia empresas con perfil ejecutable.',
                'cards' => [
                    [
                        'title' => 'Emisiones públicas',
                        'description' => 'Colocación de instrumentos de deuda en el mercado público, registrados ante el regulador y listados en bolsa de valores.',
                        'secondary_description' => 'Permite acceso a un pool amplio de inversionistas institucionales y diversificación de base de capital.',
                        'icon' => null,
                    ],
                    [
                        'title' => 'Emisiones privadas',
                        'description' => 'Colocación directa con inversionistas institucionales y calificados bajo estructuras privadas.',
                        'secondary_description' => 'Mayor flexibilidad en términos y condiciones, procesos más ágiles y menor costo regulatorio.',
                        'icon' => null,
                    ],
                ],
            ],

            'target_profile' => [
                'eyebrow' => 'PERFIL OBJETIVO',
                'title' => '¿Quién debe considerar el mercado de capitales?',
                'description' => 'Empresas con necesidades estructurales de capital, capacidad de servicio y disposición para operar bajo marcos institucionales.',
                'cards' => [
                    [
                        'title' => 'Empresas en crecimiento',
                        'description' => 'Compañías con necesidades de capital para expansión, proyectos de inversión o consolidación.',
                    ],
                    [
                        'title' => 'Refinanciamiento',
                        'description' => 'Empresas que buscan optimizar su estructura de deuda, extender plazos o reducir costos.',
                    ],
                    [
                        'title' => 'Diversificación de fuentes',
                        'description' => 'Organizaciones que desean reducir concentración en financiamiento bancario.',
                    ],
                ],
            ],

            'next_step' => [
                'eyebrow' => 'SIGUIENTE PASO',
                'title' => 'Evaluar estructura de financiamiento',
                'description' => 'Converse con nuestro equipo para determinar si una emisión pública o privada es la vía adecuada según su perfil financiero, necesidades de capital y capacidad de ejecución.',
                'button_text' => 'Contactar',
                'button_url' => '/contacto',
            ],

            'seo' => [
                'meta_title' => 'Financiamiento estructurado | GDI Valores',
                'meta_description' => 'GDI Valores diseña, documenta y ejecuta emisiones públicas y privadas para empresas que buscan diversificar sus fuentes de financiamiento.',
                'canonical_url' => '',
                'og_title' => 'Financiamiento estructurado | GDI Valores',
                'og_description' => 'Soluciones de financiamiento estructurado para empresas con necesidades de capital, refinanciamiento y expansión.',
                'og_image' => null,
            ],
        ];

        // No sobrescribir cambios posteriores hechos desde Filament.
        foreach ($sections as $section => $content) {
            PageSection::firstOrCreate(
                ['page' => 'financing', 'section' => $section],
                ['content' => $content],
            );
        }
    }
}
