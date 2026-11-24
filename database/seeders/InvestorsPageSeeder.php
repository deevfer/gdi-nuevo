<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class InvestorsPageSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'hero' => [
                'eyebrow' => 'INVERSIONISTAS',
                'title' => 'Acceso institucional a estrategias administradas con disciplina regulatoria',
                'description' => 'GDI ofrece a inversionistas institucionales, patrimoniales y tesorerías corporativas una plataforma regulada de administración de capital, con procesos formales de inversión, control de riesgos y reportería transparente.',
                'image_desktop' => null,
                'image_mobile' => null,
            ],

            'value' => [
                'eyebrow' => 'VALOR INSTITUCIONAL',
                'title' => 'Por qué asignar capital con GDI',
                'description' => 'No operamos bajo una lógica comercial masiva ni transaccional. Administramos capital bajo criterios institucionales de liquidez, riesgo, gobierno y ejecución, preservando coherencia entre mandato de inversión, estructura del vehículo y control operativo.',
                'cards' => [
                    [
                        'title' => 'Marco regulado',
                        'description' => 'Operación bajo supervisión formal del regulador de mercado de valores, con cumplimiento de requerimientos de capital, reportería y controles internos.',
                    ],
                    [
                        'title' => 'Procesos institucionales',
                        'description' => 'Comités de inversión, análisis técnico, due diligence estructurado y decisiones basadas en criterios formales de evaluación.',
                    ],
                    [
                        'title' => 'Gestión de riesgos',
                        'description' => 'Marcos de gestión de riesgos con límites de exposición, diversificación y monitoreo continuo de carteras.',
                    ],
                    [
                        'title' => 'Transparencia',
                        'description' => 'Reportería periódica a inversionistas, divulgación de metodologías y comunicación formal de resultados.',
                    ],
                ],
            ],

            'strategies' => [
                'eyebrow' => 'INVERSIONISTAS',
                'kicker' => 'PATRIMONIALES, INSTITUCIONALES Y TESORERÍAS CORPORATIVAS',
                'title' => 'Estrategias de inversión con control de riesgos y ejecución disciplinada',
                'button_text' => 'EVALUAR ACCESO COMO INVERSIONISTA',
                'button_url' => '/contacto',
                'background_image' => null,
            ],

            'process' => [
                'eyebrow' => 'ONBOARDING',
                'title' => 'Proceso de inversión',
                'description' => 'Cinco etapas formales que aseguran alineación entre perfil del inversionista, estrategia seleccionada y ejecución operativa.',
                'steps' => [
                    ['number' => '01', 'title' => 'Evaluación de perfil', 'description' => 'Análisis de objetivos de inversión, tolerancia al riesgo, horizonte temporal y necesidades de liquidez.'],
                    ['number' => '02', 'title' => 'Selección de estrategia', 'description' => 'Definición de la estrategia de inversión adecuada según el perfil del inversionista.'],
                    ['number' => '03', 'title' => 'Documentación', 'description' => 'Formalización de la relación mediante contratos de inversión y documentación regulatoria.'],
                    ['number' => '04', 'title' => 'Asignación de capital', 'description' => 'Implementación de la inversión bajo procesos formales de asignación.'],
                    ['number' => '05', 'title' => 'Seguimiento y reportería', 'description' => 'Monitoreo continuo de la inversión y comunicación periódica de resultados.'],
                ],
            ],

            'governance' => [
                'eyebrow' => 'ESTRUCTURA',
                'title' => 'Gobierno y control',
                'description' => 'Marcos formales de gobierno corporativo y control interno que aseguran operación bajo estándares institucionales.',
                'cards' => [
                    ['title' => 'Comité de Inversiones', 'description' => 'Órgano técnico responsable de evaluar y aprobar decisiones de inversión.', 'icon' => null],
                    ['title' => 'Auditoría independiente', 'description' => 'Revisión externa de estados financieros y controles internos.', 'icon' => null],
                    ['title' => 'Gestión de riesgos', 'description' => 'Identificación, medición y control de riesgos bajo marcos formales.', 'icon' => null],
                    ['title' => 'Cumplimiento regulatorio', 'description' => 'Supervisión de cumplimiento de normativa aplicable.', 'icon' => null],
                ],
            ],

            'reporting' => [
                'eyebrow' => 'COMUNICACIÓN',
                'title' => 'Reportería institucional',
                'paragraph_one' => 'Los inversionistas reciben reportería periódica que incluye composición de cartera, rendimientos, exposiciones por emisor y sector, y cumplimiento de límites de inversión.',
                'paragraph_two' => 'La comunicación se realiza bajo estándares institucionales con transparencia en metodologías y presentación de resultados.',
                'list_title' => 'Reportes incluyen',
                'items' => [
                    ['text' => 'Estado de cuenta mensual'],
                    ['text' => 'Composición de cartera'],
                    ['text' => 'Análisis de rentabilidad'],
                    ['text' => 'Exposición por sector y emisor'],
                    ['text' => 'Cumplimiento de límites'],
                    ['text' => 'Comentarios de mercado'],
                ],
                'background_image' => null,
            ],

            'next_step' => [
                'eyebrow' => 'SIGUIENTE PASO',
                'title' => 'Información adicional',
                'description' => 'Para mayor información sobre el proceso de inversión, estrategias disponibles y condiciones de acceso, contacte a nuestro equipo.',
                'button_text' => 'Contactar',
                'button_url' => '/contacto',
            ],

            'seo' => [
                'meta_title' => 'Inversionistas | GDI Valores',
                'meta_description' => 'Acceso institucional a estrategias de inversión administradas con disciplina regulatoria, control de riesgos y reportería transparente.',
                'canonical_url' => '',
                'og_title' => 'Inversionistas | GDI Valores',
                'og_description' => 'Plataforma regulada de administración de capital para inversionistas institucionales, patrimoniales y tesorerías corporativas.',
                'og_image' => null,
            ],
        ];

        // No sobrescribir cambios posteriores hechos desde Filament.
        foreach ($sections as $section => $content) {
            PageSection::firstOrCreate(
                ['page' => 'investors', 'section' => $section],
                ['content' => $content],
            );
        }
    }
}
