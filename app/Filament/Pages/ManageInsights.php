<?php

namespace App\Filament\Pages;

use App\Filament\Support\ManagePageSections;
use BackedEnum;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageInsights extends ManagePageSections
{
    protected static ?string $title = 'Administrar Insights';

    protected static ?string $navigationLabel = 'Insights';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'manage-insights';

    protected function pageKey(): string
    {
        return 'insights';
    }

    protected function imageDirectory(): string
    {
        return 'gdi/insights';
    }

    protected function sections(): array
    {
        return [
            'hero' => '1. Portada',
            'listing' => '2. Artículos',
            'categories' => '3. Categorías',
            'subscribe' => '4. Suscripción',
            'seo' => '5. SEO',
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

            'listing' => [
                Section::make('Textos del listado')
                    ->description('Los artículos se administran en el menú "Artículos de Insights".')
                    ->schema([
                        $this->text('listing.featured_label', 'Etiqueta del artículo destacado'),
                        $this->text('listing.read_more', 'Texto del enlace "Leer más"'),
                        $this->text('listing.empty_text', 'Mensaje cuando no hay artículos'),
                    ]),
            ],

            'categories' => [
                $this->heading('categories', 'Encabezado', withDescription: false),
                Section::make('Categorías')
                    ->description('Los botones filtran los artículos por categoría. Use el mismo nombre en cada artículo.')
                    ->schema([
                        $this->openRepeater('categories.items', 'Categorías', [
                            $this->text('name', 'Nombre')->required(),
                        ], 'name'),
                    ]),
            ],

            'subscribe' => [
                Section::make('Bloque de suscripción')
                    ->schema([
                        $this->text('subscribe.eyebrow', 'Etiqueta superior'),
                        $this->area('subscribe.title', 'Título principal', 2)->required(),
                        $this->area('subscribe.description', 'Descripción', 4),
                        ...$this->button('subscribe'),
                    ]),
            ],

            'seo' => $this->seoSchema(),

            default => [],
        };
    }
}
