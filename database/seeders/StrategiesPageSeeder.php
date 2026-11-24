<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class StrategiesPageSeeder extends Seeder
{
    public function run(): void
    {
        $faqQuestions = [
            '¿Cuál es el objetivo del fondo?',
            '¿En qué tipo de activos invierte?',
            '¿Cuál es el perfil de riesgo del fondo?',
            '¿Cuál es el horizonte de inversión recomendado?',
            '¿Cómo funciona la liquidez del fondo?',
            '¿Qué tipo de inversionistas pueden participar?',
            '¿El fondo garantiza rendimientos?',
            '¿Cómo se realiza el proceso de inversión?',
        ];

        // Las respuestas no vienen en el diseño: el equipo las completa desde el CMS.
        $faqs = array_map(fn (string $question): array => ['question' => $question, 'answer' => ''], $faqQuestions);

        // Puntos tomados de la gráfica del diseño (base 100). Se reemplazan desde el CMS.
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $values = [100.1, 101.3, 102.5, 103.1, 104.3, 105.8, 106.5, 107.9, 108.2, 109.6, 110.2, 111.4];
        $chart = array_map(fn (string $label, float $value): array => ['label' => $label, 'value' => $value], $months, $values);

        $sharedFund = [
            'subtitle' => 'Estrategia',
            'features_title' => 'Características',
            'profile_title' => 'Perfil de inversionista',
            'performance_title' => 'Performance',
            'performance_subtitle' => 'Resultados históricos de la estrategia',
            'returns_period_label' => 'Periodo',
            'returns_value_label' => 'Retorno (%)',
            'chart' => $chart,
            'disclaimer' => 'Los resultados pasados no garantizan rendimientos futuros. La rentabilidad puede variar dependiendo de las condiciones del mercado.',
            'faq_title' => 'Información para inversionistas',
            'faq_subtitle' => 'Aspectos clave sobre la estructura, operación y funcionamiento del fondo',
            'faqs' => $faqs,
        ];

        $sections = [
            'hero' => [
                'eyebrow' => 'ESTRATEGIAS DE INVERSIÓN',
                'title' => 'GDI es una plataforma institucional que administra capital con disciplina',
                'description' => 'Las estrategias de inversión de GDI forman parte de una arquitectura institucional de administración de capital, definida por criterios de liquidez, riesgo, mandato y control operativo.',
                'image_desktop' => null,
                'image_mobile' => null,
            ],

            'decisions' => [
                'eyebrow' => 'PROCESO',
                'title' => 'Cómo tomamos decisiones de inversión',
                'description' => 'No construimos portafolios sobre narrativas de mercado. Cada decisión de inversión responde a análisis técnico, estructuras definidas y criterios de riesgo explícitos dentro de un mismo marco institucional.',
                'principles' => [
                    ['title' => 'Gestión de riesgo como función central', 'description' => 'El riesgo define la asignación. No es una función de soporte, es el eje del proceso de inversión.'],
                    ['title' => 'Disciplina estructural', 'description' => 'Las inversiones se ejecutan bajo estructuras predefinidas, no decisiones discrecionales.'],
                    ['title' => 'Transparencia operativa', 'description' => 'Cada inversión es medible, documentada y auditable.'],
                ],
            ],

            'approach' => [
                'eyebrow' => 'FILOSOFÍA',
                'title' => 'Enfoque de inversión',
                'cards' => [
                    ['title' => 'Preservación de capital', 'description' => 'Prioridad en la protección del capital a través de selección disciplinada y diversificación.', 'icon' => null],
                    ['title' => 'Gestión de liquidez', 'description' => 'Optimización de la liquidez disponible bajo criterios de eficiencia y acceso.', 'icon' => null],
                    ['title' => 'Análisis crediticio', 'description' => 'Evaluación técnica de emisores, sectores y estructuras bajo procesos formales.', 'icon' => null],
                    ['title' => 'Seguimiento continuo', 'description' => 'Monitoreo activo de carteras y ajustes según evolución de mercado.', 'icon' => null],
                ],
            ],

            'funds_intro' => [
                'eyebrow' => 'INCOME & LIQUIDITY',
                'title' => 'Estrategias de liquidez e ingreso',
                'description' => 'Vehículos de inversión enfocados en gestión de liquidez, preservación de capital y generación de ingreso a través de asignación en renta fija institucional.',
            ],

            'funds' => [
                'items' => [
                    [
                        ...$sharedFund,
                        'code' => 'USD',
                        'name' => 'GDI Income & Liquidity Fund – USD',
                        'paragraph_one' => 'Inversión en instrumentos de renta fija de emisores públicos y privados en mercados locales e internacionales, bajo criterios de liquidez, diversificación y control de riesgo.',
                        'paragraph_two' => 'El fondo busca optimizar la gestión de liquidez a través de una cartera diversificada de instrumentos de corta duración, manteniendo estándares de calidad crediticia y procesos disciplinados de selección.',
                        'features' => [
                            ['label' => 'Moneda', 'value' => 'USD', 'highlight' => true],
                            ['label' => 'Estructura', 'value' => 'Fondo abierto', 'highlight' => false],
                            ['label' => 'Inversión mínima', 'value' => 'US$25,000', 'highlight' => true],
                            ['label' => 'Liquidez', 'value' => 'Condicionada diaria', 'highlight' => false],
                            ['label' => 'Permanencia mínima', 'value' => '90 días', 'highlight' => false],
                            ['label' => 'Comisión', 'value' => 'Hasta 1.5% anual', 'highlight' => false],
                            ['label' => 'Registro', 'value' => 'RMVM-NA-96/2022', 'highlight' => true],
                            ['label' => 'Fecha de inicio del fondo', 'value' => '13-OCT-2022', 'highlight' => false],
                        ],
                        'profile_text' => 'Dirigido a inversionistas institucionales, family offices, wealth managers y tesorerías corporativas que buscan optimizar la gestión de liquidez bajo un enfoque conservador dentro de una estructura institucional.',
                        'returns' => [
                            ['period' => 'Mensual (último mes)', 'value' => '0.46%'],
                            ['period' => 'Year-to-Date (YTD)', 'value' => '1.36%'],
                            ['period' => 'Anualizado', 'value' => '6.35%'],
                            ['period' => 'Desde inicio', 'value' => '24.81%'],
                        ],
                    ],
                    [
                        ...$sharedFund,
                        'code' => 'GTQ',
                        'name' => 'GDI Income & Liquidity Fund – GTQ',
                        'paragraph_one' => 'Inversión en instrumentos de renta fija en moneda local, priorizando liquidez, estabilidad y preservación de capital dentro del mercado guatemalteco.',
                        'paragraph_two' => 'Asignación disciplinada en emisores locales con criterios de calidad crediticia, bajo un marco de gestión de riesgos institucional y procesos formales de selección.',
                        'features' => [
                            ['label' => 'Moneda', 'value' => 'GTQ', 'highlight' => true],
                            ['label' => 'Estructura', 'value' => 'Fondo abierto', 'highlight' => false],
                            ['label' => 'Inversión mínima', 'value' => 'Q25,000', 'highlight' => true],
                            ['label' => 'Liquidez', 'value' => 'Condicionada', 'highlight' => false],
                            ['label' => 'Permanencia mínima', 'value' => '15 días', 'highlight' => false],
                            ['label' => 'Comisión', 'value' => 'Hasta 1.5% anual', 'highlight' => false],
                            ['label' => 'Registro', 'value' => 'RMVM-NA-2022', 'highlight' => true],
                            ['label' => 'Fecha de inicio del fondo', 'value' => '28-OCT-2022', 'highlight' => false],
                        ],
                        'profile_text' => 'Orientado a inversionistas que requieren gestión de liquidez en moneda local con criterios de estabilidad, acceso eficiente y administración institucional.',
                        'returns' => [
                            ['period' => 'Mensual (último mes)', 'value' => '0.52%'],
                            ['period' => 'Year-to-Date (YTD)', 'value' => '5.80%'],
                            ['period' => 'Anualizado', 'value' => '7.20%'],
                            ['period' => 'Desde inicio', 'value' => '14.80%'],
                        ],
                    ],
                ],
            ],

            'next_step' => [
                'eyebrow' => 'SIGUIENTE PASO',
                'title' => 'Información adicional',
                'description' => 'Para mayor información sobre estrategias de inversión, características de los fondos y proceso de asignación, contacte a nuestro equipo de relaciones con inversionistas.',
                'button_text' => 'Contactar',
                'button_url' => '/contacto',
            ],

            'seo' => [
                'meta_title' => 'Estrategias de inversión | GDI Valores',
                'meta_description' => 'Estrategias de liquidez e ingreso administradas bajo criterios institucionales de liquidez, riesgo, mandato y control operativo.',
                'canonical_url' => '',
                'og_title' => 'Estrategias de inversión | GDI Valores',
                'og_description' => 'Conozca los fondos GDI Income & Liquidity en USD y GTQ: características, perfil de inversionista y resultados históricos.',
                'og_image' => null,
            ],
        ];

        // No sobrescribir cambios posteriores hechos desde Filament.
        foreach ($sections as $section => $content) {
            PageSection::firstOrCreate(
                ['page' => 'strategies', 'section' => $section],
                ['content' => $content],
            );
        }
    }
}
