<?php

namespace App\Filament\Resources\Insights\Schemas;

use App\Filament\Components\SearchSnippet;
use App\Models\Insight;
use App\Models\PageSection;
use App\Services\SiteSeo;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class InsightForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Grid::make(1)
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Contenido')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?string $old): void {
                                        // Genera el slug mientras el usuario no lo haya personalizado.
                                        if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                            $set('slug', Str::slug((string) $state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->label('URL del artículo')
                                    ->prefix('/insights/')
                                    ->required()
                                    ->maxLength(255)
                                    ->alphaDash()
                                    ->unique(ignoreRecord: true),
                                Textarea::make('excerpt')
                                    ->label('Resumen')
                                    ->helperText('Aparece en las tarjetas del listado.')
                                    ->rows(3),
                                RichEditor::make('body')
                                    ->label('Cuerpo del artículo')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('gdi/insights/contenido')
                                    ->fileAttachmentsVisibility('public'),
                            ]),
                        Section::make('SEO')
                            ->collapsed()
                            ->schema([
                                SearchSnippet::make(
                                    ['meta_title', 'title'],
                                    ['meta_description', 'excerpt'],
                                    fn (?Insight $record, Get $get): string => app(SiteSeo::class)->absolute('/insights/'.($get('slug') ?: $record?->slug)),
                                ),
                                TextInput::make('meta_title')
                                    ->label('Metatítulo')
                                    ->helperText('Recomendado: hasta 48 caracteres (se agrega " | GDI Valores"). Vacío usa el título del artículo.')
                                    ->maxLength(255)
                                    ->live(debounce: 500),
                                Textarea::make('meta_description')
                                    ->label('Metadescripción')
                                    ->helperText('Recomendado: entre 70 y 160 caracteres. Vacía usa el resumen.')
                                    ->rows(3)
                                    ->live(debounce: 500),
                                TextInput::make('og_image_alt')
                                    ->label('Texto alternativo de la imagen al compartir')
                                    ->helperText('Vacío usa el título del artículo.')
                                    ->maxLength(255),
                                Toggle::make('robots_index')
                                    ->label('Permitir que los buscadores indexen este artículo')
                                    ->default(true)
                                    ->helperText('Apagado agrega noindex y lo quita del sitemap.'),
                            ]),
                    ]),
                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Publicación')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Publicado')
                                    ->default(true),
                                DatePicker::make('published_at')
                                    ->label('Fecha de publicación')
                                    ->default(now())
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                                Toggle::make('is_featured')
                                    ->label('Destacado')
                                    ->helperText('El artículo destacado más reciente aparece en grande al inicio de Insights.'),
                                TextInput::make('category')
                                    ->label('Categoría')
                                    ->datalist(fn (): array => self::categories())
                                    ->helperText('Use una de las categorías de la página Insights para que el filtro funcione.'),
                            ]),
                        Section::make('Imagen')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Imagen principal')
                                    ->image()
                                    ->disk('public')
                                    ->directory('gdi/insights')
                                    ->visibility('public'),
                            ]),
                    ]),
            ]);
    }

    /** Categorías configuradas en la página Insights del CMS. */
    private static function categories(): array
    {
        $items = PageSection::query()
            ->where('page', 'insights')
            ->where('section', 'categories')
            ->value('content')['items'] ?? [];

        return collect($items)->pluck('name')->filter()->values()->all();
    }
}
