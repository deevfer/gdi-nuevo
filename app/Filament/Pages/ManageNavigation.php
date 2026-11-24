<?php

namespace App\Filament\Pages;

use App\Filament\Support\ManagePageSections;
use BackedEnum;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

/** Menú superior, pie de página y textos del aviso de cookies: se comparten en todas las páginas. */
class ManageNavigation extends ManagePageSections
{
    protected static ?string $title = 'Administrar menú, pie de página y cookies';

    protected static ?string $navigationLabel = 'Menú y pie de página';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static ?int $navigationSort = 8;

    protected static ?string $slug = 'manage-navigation';

    protected function pageKey(): string
    {
        return 'global';
    }

    protected function imageDirectory(): string
    {
        return 'gdi/global';
    }

    protected function previewPath(?string $statePath = null): string
    {
        return '/';
    }

    protected function sections(): array
    {
        return [
            'header' => '1. Menú superior',
            'footer' => '2. Pie de página',
            'cookies' => '3. Aviso de cookies',
        ];
    }

    protected function sectionSchema(string $section): array
    {
        $link = [
            $this->text('label', 'Texto')->required(),
            $this->text('url', 'Enlace')->required()->helperText('Ruta interna (/contacto) o URL completa.'),
        ];

        return match ($section) {
            'header' => [
                Section::make('Menú superior')
                    ->schema([
                        $this->text('header.brand_name', 'Nombre de la marca')->required(),
                        $this->openRepeater('header.links', 'Enlaces del menú', $link, 'label'),
                    ]),
            ],

            'footer' => [
                Section::make('Marca')
                    ->schema([
                        $this->text('footer.brand_name', 'Nombre de la marca'),
                        $this->area('footer.description', 'Descripción', 3),
                    ]),
                Section::make('Columnas de enlaces')
                    ->schema([
                        $this->openRepeater('footer.groups', 'Columnas', [
                            $this->text('title', 'Título de la columna')->required(),
                            $this->openRepeater('links', 'Enlaces', $link, 'label'),
                        ]),
                    ]),
                Section::make('Franja inferior')
                    ->schema([
                        $this->text('footer.copyright', 'Derechos reservados')
                            ->helperText('Use {año} para el año actual. Ejemplo: © {año} GDI Valores.'),
                        $this->text('footer.bottom_note', 'Nota a la derecha'),
                    ])
                    ->columns(2),
            ],

            'cookies' => [
                Section::make('Aviso de cookies')
                    ->description('Aparece la primera vez que alguien visita el sitio. Las etiquetas de Google, Meta y LinkedIn solo se cargan si la persona las acepta.')
                    ->schema([
                        $this->text('cookies.title', 'Título'),
                        $this->area('cookies.body', 'Texto', 3),
                        $this->text('cookies.accept', 'Botón aceptar'),
                        $this->text('cookies.reject', 'Botón rechazar'),
                        $this->text('cookies.configure', 'Botón configurar'),
                        $this->text('cookies.save', 'Botón guardar preferencias'),
                        $this->text('cookies.trigger', 'Botón para volver a abrir el aviso'),
                    ])
                    ->columns(2),
                Section::make('Categorías')
                    ->schema([
                        $this->text('cookies.essential_title', 'Esenciales: título'),
                        $this->text('cookies.essential_body', 'Esenciales: descripción'),
                        $this->text('cookies.analytics_title', 'Analítica: título'),
                        $this->text('cookies.analytics_body', 'Analítica: descripción'),
                        $this->text('cookies.marketing_title', 'Marketing: título'),
                        $this->text('cookies.marketing_body', 'Marketing: descripción'),
                    ])
                    ->columns(2),
            ],

            default => [],
        };
    }
}
