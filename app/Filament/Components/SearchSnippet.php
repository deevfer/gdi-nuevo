<?php

namespace App\Filament\Components;

use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;

/**
 * Cómo se vería la página en un resultado de Google, actualizado mientras se escriben el título y la descripción.
 */
class SearchSnippet
{
    /**
     * @param  array<int, string>  $titleFields  Se usa el primer campo con valor (p. ej. título SEO y luego el título del artículo).
     * @param  array<int, string>  $descriptionFields
     * @param  Closure(mixed, Get): string  $url  Recibe el registro que se está editando y el estado del formulario.
     */
    public static function make(array $titleFields, array $descriptionFields, Closure $url): View
    {
        $first = fn (Get $get, array $fields): string => collect($fields)->map(fn (string $field): string => trim(strip_tags((string) $get($field))))->first(fn (string $value): bool => $value !== '') ?? '';

        return View::make('filament.components.search-snippet')
            ->viewData(fn (Get $get, mixed $record = null): array => [
                'title' => $first($get, $titleFields),
                'description' => $first($get, $descriptionFields),
                'url' => $url($record, $get),
            ])
            ->columnSpanFull();
    }
}
