<?php

namespace App\Filament\Pages;

use App\Filament\Support\ManagePageSections;
use BackedEnum;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageInvestors extends ManagePageSections
{
    protected static ?string $title = 'Administrar Inversionistas';

    protected static ?string $navigationLabel = 'Inversionistas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'manage-investors';

    protected function pageKey(): string
    {
        return 'investors';
    }

    protected function imageDirectory(): string
    {
        return 'gdi/inversionistas';
    }

    protected function sections(): array
    {
        return [
            'hero' => '1. Portada',
            'value' => '2. Valor institucional',
            'strategies' => '3. Estrategias',
            'process' => '4. Proceso de inversión',
            'governance' => '5. Gobierno y control',
            'reporting' => '6. Reportería',
            'next_step' => '7. Siguiente paso',
            'seo' => '8. SEO',
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

            'value' => [
                $this->heading('value'),
                Section::make('Cuatro pilares')
                    ->schema([
                        $this->fixedRepeater('value.cards', 'Pilares', [
                            $this->text('title', 'Título')->required(),
                            $this->area('description', 'Descripción', 4),
                        ], 4),
                    ]),
            ],

            'strategies' => [
                Section::make('Bloque de estrategias')
                    ->schema([
                        $this->text('strategies.eyebrow', 'Etiqueta superior'),
                        $this->text('strategies.kicker', 'Texto secundario superior'),
                        $this->area('strategies.title', 'Título principal', 3)->required(),
                        ...$this->button('strategies'),
                        $this->image('strategies.background_image', 'Fotografía de fondo'),
                    ]),
            ],

            'process' => [
                $this->heading('process'),
                Section::make('Cinco etapas')
                    ->description('Las etapas alternan color bronce y blanco en el orden del diseño.')
                    ->schema([
                        $this->fixedRepeater('process.steps', 'Etapas', [
                            $this->text('number', 'Número')->required(),
                            $this->text('title', 'Nombre de la etapa')->required(),
                            $this->area('description', 'Descripción', 3),
                        ], 5),
                    ]),
            ],

            'governance' => [
                $this->heading('governance'),
                Section::make('Cuatro órganos de control')
                    ->schema([
                        $this->fixedRepeater('governance.cards', 'Órganos', [
                            $this->text('title', 'Título')->required(),
                            $this->area('description', 'Descripción', 3),
                            $this->image('icon', 'Icono (opcional)'),
                        ], 4),
                    ]),
            ],

            'reporting' => [
                Section::make('Texto de reportería')
                    ->schema([
                        $this->text('reporting.eyebrow', 'Etiqueta superior'),
                        $this->text('reporting.title', 'Título principal')->required(),
                        $this->area('reporting.paragraph_one', 'Primer párrafo', 4),
                        $this->area('reporting.paragraph_two', 'Segundo párrafo', 4),
                    ]),
                Section::make('Recuadro "Reportes incluyen"')
                    ->schema([
                        $this->text('reporting.list_title', 'Título del recuadro'),
                        $this->openRepeater('reporting.items', 'Elementos', [
                            $this->text('text', 'Texto')->required(),
                        ], 'text'),
                    ]),
                Section::make('Fondo')
                    ->schema([
                        $this->image('reporting.background_image', 'Fotografía de fondo'),
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
}
