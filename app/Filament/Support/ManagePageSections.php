<?php

namespace App\Filament\Support;

use App\Filament\Concerns\HasLivePreview;
use App\Models\PageSection;
use App\Services\SiteSeo;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use UnitEnum;

/**
 * Base para las páginas del CMS que editan las secciones de una página del sitio.
 * Cada sección se guarda como un registro de page_sections (page + section + content JSON).
 */
abstract class ManagePageSections extends Page
{
    use HasLivePreview;

    protected static string|UnitEnum|null $navigationGroup = 'Páginas del sitio';

    protected string $view = 'filament.pages.manage-page-sections';

    public ?array $data = [];

    /** Clave de la página en page_sections (por ejemplo: investors). */
    abstract protected function pageKey(): string;

    /** Secciones en el orden del diseño: clave => etiqueta de la pestaña. */
    abstract protected function sections(): array;

    /** Campos de Filament de una sección. */
    abstract protected function sectionSchema(string $section): array;

    /** Carpeta del disco public donde se guardan las imágenes de la página. */
    abstract protected function imageDirectory(): string;

    public function mount(): void
    {
        $records = PageSection::query()
            ->where('page', $this->pageKey())
            ->get()
            ->keyBy('section');

        $state = [];

        foreach (array_keys($this->sections()) as $section) {
            $state[$section] = $records->get($section)?->content ?? [];
        }

        $this->form->fill($state);
    }

    public function form(Schema $schema): Schema
    {
        $tabs = [];

        foreach ($this->sections() as $section => $label) {
            $tabs[] = Tab::make($label)->schema($this->sectionSchema($section));
        }

        return $schema
            ->components([
                Form::make([
                    Tabs::make('Secciones')->tabs($tabs),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Guardar cambios')
                                ->submit('save'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        DB::transaction(function () use ($state): void {
            foreach (array_keys($this->sections()) as $section) {
                $record = PageSection::firstOrNew([
                    'page' => $this->pageKey(),
                    'section' => $section,
                ]);

                $previous = $record->content ?? [];
                $submitted = $state[$section] ?? [];

                $record->content = array_replace(
                    is_array($previous) ? $previous : [],
                    is_array($submitted) ? $submitted : [],
                );

                $record->save();
            }
        });

        Notification::make()
            ->title(static::$navigationLabel.' actualizado correctamente')
            ->success()
            ->send();
    }

    protected function previewPath(?string $statePath = null): string
    {
        return app(SiteSeo::class)->path($this->pageKey());
    }

    protected function text(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label);
    }

    protected function area(string $name, string $label, int $rows = 3): Textarea
    {
        return Textarea::make($name)->label($label)->rows($rows);
    }

    protected function image(string $name, string $label, ?string $directory = null): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory($directory ?? $this->imageDirectory())
            ->visibility('public');
    }

    /** Encabezado común: etiqueta, título y descripción. */
    protected function heading(string $section, string $label = 'Encabezado', bool $withDescription = true): Section
    {
        $fields = [
            $this->text("{$section}.eyebrow", 'Etiqueta superior'),
            $this->area("{$section}.title", 'Título principal', 3)->required(),
        ];

        if ($withDescription) {
            $fields[] = $this->area("{$section}.description", 'Descripción', 4);
        }

        return Section::make($label)->schema($fields);
    }

    /** Botón con texto y enlace. */
    protected function button(string $prefix): array
    {
        return [
            $this->text("{$prefix}.button_text", 'Texto del botón'),
            $this->text("{$prefix}.button_url", 'Enlace del botón')
                ->helperText('Ruta interna, por ejemplo: /contacto'),
        ];
    }

    /** Lista de elementos con cantidad fija (el diseño define cuántos hay). */
    protected function fixedRepeater(string $name, string $label, array $schema, int $count, string $titleKey = 'title'): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->schema($schema)
            ->itemLabel(fn (array $state): ?string => $state[$titleKey] ?? null)
            ->collapsed()
            ->minItems($count)
            ->maxItems($count)
            ->addable(false)
            ->deletable(false)
            ->reorderable(false);
    }

    /** Lista de elementos que el equipo puede ampliar, quitar y reordenar. */
    protected function openRepeater(string $name, string $label, array $schema, string $titleKey = 'title'): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->schema($schema)
            ->itemLabel(fn (array $state): ?string => $state[$titleKey] ?? null)
            ->collapsed()
            ->reorderable();
    }

    protected function seoSchema(): array
    {
        return SeoFields::schema($this->pageKey());
    }
}
