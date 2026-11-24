<?php

namespace App\Filament\Support;

use App\Filament\Components\SearchSnippet;
use App\Services\SiteSeo;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

/**
 * Pestaña SEO común a todas las páginas del sitio (se guarda en la sección "seo" de la página).
 */
class SeoFields
{
    /** @return array<int, Section> */
    public static function schema(string $page): array
    {
        return [
            Section::make('Resultado en Google')
                ->description('Así se vería la página en los resultados de búsqueda. Se actualiza mientras escribe.')
                ->schema([
                    SearchSnippet::make(
                        ['seo.meta_title'],
                        ['seo.meta_description', 'hero.description'],
                        fn (): string => app(SiteSeo::class)->absolute(app(SiteSeo::class)->path($page)),
                    ),
                ]),
            Section::make('SEO básico')
                ->schema([
                    TextInput::make('seo.meta_title')
                        ->label('Metatítulo')
                        ->helperText('Recomendado: entre 30 y 60 caracteres. Vacío usa "'.SiteSeo::PAGES[$page]['title'].'".')
                        ->maxLength(255)
                        ->live(debounce: 500),
                    Textarea::make('seo.meta_description')
                        ->label('Metadescripción')
                        ->helperText('Recomendado: entre 70 y 160 caracteres. Vacía usa la descripción de la portada.')
                        ->rows(3)
                        ->live(debounce: 500),
                    TextInput::make('seo.canonical_url')
                        ->label('URL canónica (opcional)')
                        ->url()
                        ->helperText('Solo si esta página duplica otra. Vacía usa la URL de la página en el dominio público.'),
                ]),
            Section::make('Indexación')
                ->description('Fuera del servidor de producción el sitio siempre pide no indexar, sin importar estas opciones.')
                ->schema([
                    Toggle::make('seo.robots_index')
                        ->label('Permitir que los buscadores indexen esta página')
                        // Las páginas guardadas antes de existir este campo siguen siendo indexables.
                        ->formatStateUsing(fn (?bool $state): bool => $state ?? true)
                        ->helperText('Apagado agrega noindex y la quita del sitemap.'),
                    Toggle::make('seo.robots_follow')
                        ->label('Permitir que los buscadores sigan los enlaces de esta página')
                        ->formatStateUsing(fn (?bool $state): bool => $state ?? true),
                ])
                ->columns(2),
            Section::make('LinkedIn, WhatsApp y redes sociales (Open Graph)')
                ->schema([
                    TextInput::make('seo.og_title')->label('Título para redes sociales')->maxLength(255)->helperText('Vacío usa el metatítulo.'),
                    Textarea::make('seo.og_description')->label('Descripción para redes sociales')->rows(3)->helperText('Vacía usa la metadescripción.'),
                    FileUpload::make('seo.og_image')
                        ->label('Imagen para compartir')
                        ->image()
                        ->disk('public')
                        ->directory('gdi/seo')
                        ->visibility('public')
                        ->helperText('Recomendado: 1200 × 630 píxeles. Vacía usa la imagen de portada.'),
                    TextInput::make('seo.og_image_alt')->label('Texto alternativo de la imagen')->maxLength(255),
                ]),
        ];
    }
}
