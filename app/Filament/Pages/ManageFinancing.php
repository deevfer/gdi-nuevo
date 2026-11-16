<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
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
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class ManageFinancing extends Page
{
    protected string $view = 'filament.pages.manage-financing';

    protected static ?string $title = 'Administrar Financiamiento estructurado';
    protected static ?string $navigationLabel = 'Financiamiento estructurado';
    protected static ?string $slug = 'manage-financing';

    public ?array $data = [];

    private const PAGE_KEY = 'financing';

    private const SECTIONS = [
        'hero' => '1. Portada',
        'problem_solution' => '2. Problema y solución',
        'companies' => '3. Empresas',
        'process' => '4. Metodología',
        'instruments' => '5. Tipos de emisiones',
        'target_profile' => '6. Perfil objetivo',
        'next_step' => '7. Siguiente paso',
        'seo' => '8. SEO',
    ];

    public function mount(): void
    {
        $records = PageSection::query()
            ->where('page', self::PAGE_KEY)
            ->get()
            ->keyBy('section');

        $state = [];

        foreach (array_keys(self::SECTIONS) as $section) {
            $state[$section] = $records->get($section)?->content ?? [];
        }

        $this->form->fill($state);
    }

    public function form(Schema $schema): Schema
    {
        $tabs = [];

        foreach (self::SECTIONS as $section => $label) {
            $tabs[] = Tab::make($label)
                ->schema($this->sectionSchema($section));
        }

        return $schema
            ->components([
                Form::make([
                    Tabs::make('Secciones de Financiamiento estructurado')
                        ->tabs($tabs),
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

    private function sectionSchema(string $section): array
    {
        return match ($section) {
            'hero' => [
                Section::make('Presentación de la página')
                    ->schema([
                        $this->text('hero.eyebrow', 'Etiqueta superior'),
                        $this->area('hero.title', 'Título principal', 3)->required(),
                        $this->area('hero.description', 'Descripción', 4),
                    ]),
                Section::make('Imagen de portada')
                    ->schema([
                        $this->image('hero.image_desktop', 'Imagen de escritorio'),
                        $this->image('hero.image_mobile', 'Imagen para móvil (opcional)'),
                    ])
                    ->columns(2),
            ],

            'problem_solution' => [
                Section::make('Columna: El problema')
                    ->schema([
                        $this->text('problem_solution.problem_eyebrow', 'Etiqueta'),
                        $this->text('problem_solution.problem_title', 'Título')->required(),
                        $this->area('problem_solution.problem_paragraph_one', 'Primer párrafo', 4),
                        $this->area('problem_solution.problem_paragraph_two', 'Segundo párrafo', 4),
                    ]),
                Section::make('Columna: La solución')
                    ->schema([
                        $this->text('problem_solution.solution_eyebrow', 'Etiqueta'),
                        $this->text('problem_solution.solution_title', 'Título')->required(),
                        $this->area('problem_solution.solution_paragraph_one', 'Primer párrafo', 4),
                        $this->area('problem_solution.solution_paragraph_two', 'Segundo párrafo', 4),
                    ]),
            ],

            'companies' => [
                Section::make('Bloque Empresas')
                    ->schema([
                        $this->text('companies.eyebrow', 'Etiqueta superior'),
                        $this->text('companies.kicker', 'Texto secundario superior'),
                        $this->area('companies.title', 'Título principal', 4)->required(),
                        $this->text('companies.button_text', 'Texto del botón'),
                        $this->text('companies.button_url', 'Enlace del botón')
                            ->helperText('Ruta interna, por ejemplo: /contacto'),
                        $this->image('companies.background_image', 'Fotografía de fondo'),
                    ]),
            ],

            'process' => [
                Section::make('Encabezado de metodología')
                    ->schema([
                        $this->text('process.eyebrow', 'Etiqueta superior'),
                        $this->area('process.title', 'Título principal', 3)->required(),
                        $this->area('process.description', 'Descripción', 4),
                    ]),
                Section::make('Tres etapas del proceso')
                    ->description('Los pasos 01, 02 y 03 y sus tres etiquetas se mantienen en el orden del diseño.')
                    ->schema([
                        Repeater::make('process.steps')
                            ->label('Etapas')
                            ->schema([
                                $this->text('number', 'Número')->required(),
                                $this->text('title', 'Nombre de etapa')->required(),
                                $this->area('description', 'Descripción de la etapa', 4),
                                Repeater::make('items')
                                    ->label('Tres elementos de la etapa')
                                    ->schema([
                                        $this->text('text', 'Texto')->required(),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['text'] ?? 'Elemento')
                                    ->collapsed()
                                    ->minItems(3)
                                    ->maxItems(3)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false),
                            ])
                            ->itemLabel(fn (array $state): ?string => trim(($state['number'] ?? '') . ' · ' . ($state['title'] ?? 'Etapa')))
                            ->collapsed()
                            ->minItems(3)
                            ->maxItems(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            'instruments' => [
                Section::make('Encabezado de instrumentos')
                    ->schema([
                        $this->text('instruments.eyebrow', 'Etiqueta superior'),
                        $this->area('instruments.title', 'Título principal', 3)->required(),
                        $this->area('instruments.description', 'Descripción', 4),
                    ]),
                Section::make('Dos tipos de emisiones')
                    ->description('La presentación visual de las dos tarjetas queda definida en Vue y CSS.')
                    ->schema([
                        Repeater::make('instruments.cards')
                            ->label('Emisiones')
                            ->schema([
                                $this->text('title', 'Título')->required(),
                                $this->area('description', 'Descripción principal', 4),
                                $this->area('secondary_description', 'Descripción adicional', 4),
                                $this->image('icon', 'Icono (opcional)'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Emisión')
                            ->collapsed()
                            ->minItems(2)
                            ->maxItems(2)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            'target_profile' => [
                Section::make('Encabezado del perfil objetivo')
                    ->schema([
                        $this->text('target_profile.eyebrow', 'Etiqueta superior'),
                        $this->area('target_profile.title', 'Título principal', 3)->required(),
                        $this->area('target_profile.description', 'Descripción', 4),
                    ]),
                Section::make('Tres perfiles')
                    ->schema([
                        Repeater::make('target_profile.cards')
                            ->label('Perfiles')
                            ->schema([
                                $this->text('title', 'Título')->required(),
                                $this->area('description', 'Descripción', 4),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Perfil')
                            ->collapsed()
                            ->minItems(3)
                            ->maxItems(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            'next_step' => [
                Section::make('Cierre de la página')
                    ->schema([
                        $this->text('next_step.eyebrow', 'Etiqueta superior'),
                        $this->area('next_step.title', 'Título principal', 3)->required(),
                        $this->area('next_step.description', 'Descripción', 4),
                        $this->text('next_step.button_text', 'Texto del botón'),
                        $this->text('next_step.button_url', 'Enlace del botón'),
                    ]),
            ],

            'seo' => [
                Section::make('SEO básico')
                    ->schema([
                        $this->text('seo.meta_title', 'Metatítulo')->maxLength(255),
                        $this->area('seo.meta_description', 'Metadescripción', 4),
                        $this->text('seo.canonical_url', 'URL canónica (opcional)')
                            ->helperText('Si se deja vacía, se podrá generar desde la URL de la página.'),
                    ]),
                Section::make('LinkedIn y redes sociales (Open Graph)')
                    ->schema([
                        $this->text('seo.og_title', 'Título para redes sociales')->maxLength(255),
                        $this->area('seo.og_description', 'Descripción para redes sociales', 4),
                        $this->image('seo.og_image', 'Imagen para redes sociales', 'gdi/seo')
                            ->helperText('Recomendado: 1200 × 630 píxeles.'),
                    ]),
            ],

            default => [],
        };
    }

    private function text(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label);
    }

    private function area(string $name, string $label, int $rows = 3): Textarea
    {
        return Textarea::make($name)->label($label)->rows($rows);
    }

    private function image(string $name, string $label, string $directory = 'gdi/financiamiento'): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        DB::transaction(function () use ($state): void {
            foreach (array_keys(self::SECTIONS) as $section) {
                $record = PageSection::firstOrNew([
                    'page' => self::PAGE_KEY,
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
            ->title('Financiamiento estructurado actualizado correctamente')
            ->success()
            ->send();
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
