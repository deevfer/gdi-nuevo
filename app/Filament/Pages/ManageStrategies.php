<?php

namespace App\Filament\Pages;

use App\Filament\Support\ManagePageSections;
use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageStrategies extends ManagePageSections
{
    protected static ?string $title = 'Administrar Estrategias de inversión';

    protected static ?string $navigationLabel = 'Estrategias de inversión';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'manage-strategies';

    protected function pageKey(): string
    {
        return 'strategies';
    }

    protected function imageDirectory(): string
    {
        return 'gdi/estrategias';
    }

    protected function sections(): array
    {
        return [
            'hero' => '1. Portada',
            'decisions' => '2. Toma de decisiones',
            'approach' => '3. Enfoque de inversión',
            'funds_intro' => '4. Introducción de fondos',
            'funds' => '5. Fondos',
            'next_step' => '6. Siguiente paso',
            'seo' => '7. SEO',
        ];
    }

    protected function sectionSchema(string $section): array
    {
        return match ($section) {
            'hero' => [
                $this->heading('hero', 'Presentación de la página'),
                Section::make('Imagen de portada')
                    ->schema([
                        $this->image('hero.image_desktop', 'Imagen de escritorio'),
                        $this->image('hero.image_mobile', 'Imagen para móvil (opcional)'),
                    ])
                    ->columns(2),
            ],

            'decisions' => [
                $this->heading('decisions'),
                Section::make('Tres principios')
                    ->schema([
                        $this->fixedRepeater('decisions.principles', 'Principios', [
                            $this->text('title', 'Título')->required(),
                            $this->area('description', 'Descripción', 3),
                        ], 3),
                    ]),
            ],

            'approach' => [
                $this->heading('approach', 'Encabezado', withDescription: false),
                Section::make('Cuatro pilares')
                    ->description('Las tarjetas alternan color bronce y blanco en el orden del diseño.')
                    ->schema([
                        $this->fixedRepeater('approach.cards', 'Pilares', [
                            $this->text('title', 'Título')->required(),
                            $this->area('description', 'Descripción', 3),
                            $this->image('icon', 'Icono (opcional)'),
                        ], 4),
                    ]),
            ],

            'funds_intro' => [
                $this->heading('funds_intro', 'Franja de introducción'),
            ],

            'funds' => [
                Section::make('Fondos')
                    ->description('Cada fondo muestra características, perfil, rendimientos, gráfica y preguntas frecuentes.')
                    ->schema([
                        $this->openRepeater('funds.items', 'Fondos', $this->fundSchema(), 'name'),
                    ]),
            ],

            'next_step' => [
                Section::make('Cierre de la página')
                    ->schema([
                        $this->text('next_step.eyebrow', 'Etiqueta superior'),
                        $this->area('next_step.title', 'Título principal', 2)->required(),
                        $this->area('next_step.description', 'Descripción', 4),
                        ...$this->button('next_step'),
                    ]),
            ],

            'seo' => $this->seoSchema(),

            default => [],
        };
    }

    private function fundSchema(): array
    {
        return [
            Section::make('Presentación del fondo')
                ->schema([
                    $this->text('code', 'Etiqueta (por ejemplo: USD)'),
                    $this->text('name', 'Nombre del fondo')->required(),
                    $this->text('subtitle', 'Subtítulo'),
                    $this->area('paragraph_one', 'Primer párrafo', 3),
                    $this->area('paragraph_two', 'Segundo párrafo', 3),
                ]),
            Section::make('Características')
                ->schema([
                    $this->text('features_title', 'Título del recuadro'),
                    $this->openRepeater('features', 'Filas', [
                        $this->text('label', 'Concepto')->required(),
                        $this->text('value', 'Valor')->required(),
                        Toggle::make('highlight')->label('Destacar con línea lateral'),
                    ], 'label'),
                ]),
            Section::make('Perfil de inversionista')
                ->schema([
                    $this->text('profile_title', 'Título'),
                    $this->area('profile_text', 'Descripción', 3),
                ]),
            Section::make('Performance')
                ->schema([
                    $this->text('performance_title', 'Título'),
                    $this->text('performance_subtitle', 'Subtítulo'),
                    $this->text('returns_period_label', 'Encabezado de la columna de periodo'),
                    $this->text('returns_value_label', 'Encabezado de la columna de retorno'),
                    $this->openRepeater('returns', 'Rendimientos', [
                        $this->text('period', 'Periodo')->required(),
                        $this->text('value', 'Retorno')->required(),
                    ], 'period'),
                    $this->openRepeater('chart', 'Puntos de la gráfica', [
                        $this->text('label', 'Etiqueta (mes)')->required(),
                        $this->text('value', 'Valor (base 100)')->numeric()->required(),
                    ], 'label')
                        ->helperText('La gráfica se dibuja con estos puntos, en este orden.')
                        ->grid(3),
                    $this->area('disclaimer', 'Nota legal', 2),
                ]),
            Section::make('Preguntas frecuentes')
                ->schema([
                    $this->text('faq_title', 'Título'),
                    $this->text('faq_subtitle', 'Subtítulo'),
                    $this->openRepeater('faqs', 'Preguntas', [
                        $this->text('question', 'Pregunta')->required(),
                        $this->area('answer', 'Respuesta', 4),
                    ], 'question'),
                ]),
        ];
    }
}
