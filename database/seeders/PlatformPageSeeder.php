<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PlatformPageSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'hero' => [
                'eyebrow' => 'PLATAFORMA',
                'title' => 'Una arquitectura diseñada para ejecutar capital con mayor eficiencia',
                'description' => 'Integrar análisis, estructuración y ejecución dentro de una misma plataforma reduce fricción, mejora control operativo y acelera la materialización de oportunidades.',
                'image_desktop' => null,
                'image_mobile' => null,
            ],

            'context' => [
                'eyebrow' => 'CONTEXTO',
                'title' => 'La fragmentación limita la eficiencia del mercado',
                'paragraph_one' => 'Cuando capital, estructuración y ejecución operan de forma separada, aumentan los costos de coordinación, se generan desalineaciones y se reduce la velocidad de implementación.',
                'paragraph_two' => 'El modelo tradicional requiere múltiples intermediarios que no comparten marcos de decisión, creando fricción operativa y diluyendo el control sobre la calidad de ejecución.',
                'image' => null,
            ],

            'advantages' => [
                'eyebrow' => 'VENTAJAS',
                'title' => 'Integración como ventaja estructural',
                'description' => 'Un modelo integrado no solo reduce costos: redefine la capacidad de ejecutar oportunidades con mayor disciplina, velocidad y control.',
                'cards' => [
                    [
                        'title' => 'Menor fricción de coordinación',
                        'description' => 'Al eliminar intermediación entre funciones, se reducen costos transaccionales y se acelera la toma de decisiones.',
                        'icon' => null,
                    ],
                    [
                        'title' => 'Alineación entre análisis y ejecución',
                        'description' => 'Las decisiones de inversión se toman dentro del mismo marco que controla la ejecución, evitando desalineaciones.',
                        'icon' => null,
                    ],
                    [
                        'title' => 'Mayor control operativo',
                        'description' => 'Control directo sobre calidad de análisis, procesos de estructuración y estándares de cumplimiento.',
                        'icon' => null,
                    ],
                    [
                        'title' => 'Velocidad de implementación',
                        'description' => 'Las oportunidades se ejecutan con menor latencia al operar bajo una misma infraestructura institucional.',
                        'icon' => null,
                    ],
                ],
            ],

            'operation' => [
                'eyebrow' => 'FUNCIONAMIENTO',
                'title' => 'Cómo opera la plataforma',
                'description' => 'Tres funciones integradas para analizar, estructurar y ejecutar capital dentro de un mismo marco institucional.',
                'steps' => [
                    [
                        'number' => '01',
                        'title' => 'Análisis y asignación',
                        'description' => 'Evaluación técnica de oportunidades y decisiones de asignación de capital.',
                    ],
                    [
                        'number' => '02',
                        'title' => 'Estructuración',
                        'description' => 'Diseño de estructuras, instrumentos y términos para conectar capital con mayor eficiencia.',
                    ],
                    [
                        'number' => '03',
                        'title' => 'Ejecución',
                        'description' => 'Implementación y ejecución dentro de mercados públicos y privados de capital.',
                    ],
                ],
            ],

            'standards' => [
                'eyebrow' => 'ESTÁNDARES',
                'title' => 'Base institucional del modelo',
                'description' => 'La integración no reduce estándares institucionales: todos los componentes operan bajo marcos formales de regulación, gobierno corporativo y gestión de riesgos.',
                'items' => [
                    [
                        'title' => 'Regulación y supervisión',
                        'description' => 'Casa de valores registrada ante el Registro de Mercado de Valores y Mercancías y la Bolsa de Valores Nacional. Cumplimiento de requerimientos de capital, reportes y auditoría externa.',
                        'icon' => null,
                    ],
                    [
                        'title' => 'Gobierno corporativo',
                        'description' => 'Junta Directiva, Comité de inversión independientes y separación de funciones. Procesos formales de supervisión y control interno.',
                        'icon' => null,
                    ],
                    [
                        'title' => 'Gestión de riesgos',
                        'description' => 'Frameworks formales de riesgo, límites por emisor y sector, stress testing y función de riesgo independiente de inversiones.',
                        'icon' => null,
                    ],
                    [
                        'title' => 'Procesos formales',
                        'description' => 'Due diligence estructurado, procesos de aprobación documentados, reportería institucional y valoración independiente de activos.',
                        'icon' => null,
                    ],
                ],
            ],

            'ecosystem' => [
                'title' => 'Ecosistema de originación con autonomía institucional',
                'paragraph_one' => 'La relación con Aura Capital fortalece la capacidad de originación, análisis y evaluación técnica de oportunidades. GDI mantiene gobierno corporativo propio, infraestructura regulada independiente y control formal sobre la estructuración, administración y ejecución dentro del mercado de capitales.',
                'paragraph_two' => 'Esta relación amplía el acceso a oportunidades y análisis especializados, sin alterar la autonomía institucional de GDI ni sus estándares de gobierno, control y ejecución.',
                'button_text' => 'Visitar Aura Capital',
                'button_url' => '',
                'background_image' => null,
                'components_title' => 'Componentes del ecosistema',
                'components' => [
                    [
                        'title' => 'GDI Valores',
                        'description' => 'Casa de valores / Administración de capital e inversión',
                    ],
                    [
                        'title' => 'Aura Capital',
                        'description' => 'Originación y estructuración de emisiones de deuda',
                    ],
                    [
                        'title' => 'Infraestructura de análisis',
                        'description' => 'Análisis crediticio, due diligence y evaluación sectorial',
                    ],
                ],
            ],

            'next_step' => [
                'eyebrow' => 'SIGUIENTE PASO',
                'title' => 'Explorar cómo se activa la plataforma',
                'description' => 'GDI opera a través de estrategias de inversión y soluciones de financiamiento estructurado dentro del mercado de capitales.',
                'button_primary_text' => 'Ver estrategias de inversión',
                'button_primary_url' => '/estrategias-de-inversion',
                'button_secondary_text' => 'Explorar Capital Solutions',
                'button_secondary_url' => '/financiamiento-estructurado',
            ],

            'seo' => [
                'meta_title' => 'Plataforma | GDI Valores',
                'meta_description' => 'Conocé la plataforma institucional de GDI Valores, que integra análisis, estructuración y ejecución de capital con mayor eficiencia.',
                'canonical_url' => '',
                'og_title' => 'Plataforma | GDI Valores',
                'og_description' => 'Una arquitectura institucional para integrar análisis, estructuración y ejecución de capital.',
                'og_image' => null,
            ],
        ];

        foreach ($sections as $section => $content) {
            PageSection::firstOrCreate(
                ['page' => 'platform', 'section' => $section],
                ['content' => $content],
            );
        }
    }
}
