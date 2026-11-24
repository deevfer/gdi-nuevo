<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\HasLivePreview;
use App\Filament\Support\SeoFields;
use App\Models\PageSection;
use App\Services\SiteSeo;
use BackedEnum;
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
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class ManagePlatform extends Page
{
    use HasLivePreview;

    protected static string|UnitEnum|null $navigationGroup = 'Páginas del sitio';

    protected string $view = 'filament.pages.manage-platform';

    protected static ?string $title = 'Administrar Plataforma';

    protected static ?string $navigationLabel = 'Plataforma';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'manage-platform';

    public ?array $data = [];

    private const SECTIONS = [
        'hero' => '1. Portada',
        'context' => '2. Contexto',
        'advantages' => '3. Ventajas',
        'operation' => '4. Funcionamiento',
        'standards' => '5. Estándares',
        'ecosystem' => '6. Ecosistema',
        'next_step' => '7. Siguiente paso',
        'seo' => '8. SEO',
    ];

    public function mount(): void
    {
        $records = PageSection::query()
            ->where('page', 'platform')
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
                    Tabs::make('Secciones de Plataforma')
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
                Section::make('Título y presentación')
                    ->schema([
                        $this->text('hero.eyebrow', 'Etiqueta superior'),
                        $this->area('hero.title', 'Título principal', 3)->required(),
                        $this->area('hero.description', 'Descripción', 4),
                    ]),
                Section::make('Imágenes de portada')
                    ->schema([
                        $this->image('hero.image_desktop', 'Imagen de escritorio'),
                        $this->image('hero.image_mobile', 'Imagen para móvil (opcional)'),
                    ])
                    ->columns(2),
            ],

            'context' => [
                Section::make('Encabezado de contexto')
                    ->schema([
                        $this->text('context.eyebrow', 'Etiqueta superior'),
                        $this->area('context.title', 'Título principal', 3)->required(),
                    ]),
                Section::make('Contenido de contexto')
                    ->schema([
                        $this->area('context.paragraph_one', 'Primer párrafo', 5),
                        $this->area('context.paragraph_two', 'Segundo párrafo', 5),
                        $this->image('context.image', 'Imagen lateral'),
                    ]),
            ],

            'advantages' => [
                Section::make('Encabezado de ventajas')
                    ->schema([
                        $this->text('advantages.eyebrow', 'Etiqueta superior'),
                        $this->area('advantages.title', 'Título principal', 3)->required(),
                        $this->area('advantages.description', 'Descripción', 4),
                    ]),
                Section::make('Cuatro tarjetas de ventajas')
                    ->description('La disposición y los colores de las tarjetas se mantienen desde el frontend.')
                    ->schema([
                        Repeater::make('advantages.cards')
                            ->label('Ventajas')
                            ->schema([
                                $this->text('title', 'Título')->required(),
                                $this->area('description', 'Descripción', 4),
                                $this->image('icon', 'Icono'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Ventaja')
                            ->collapsed()
                            ->minItems(4)
                            ->maxItems(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            'operation' => [
                Section::make('Encabezado de funcionamiento')
                    ->schema([
                        $this->text('operation.eyebrow', 'Etiqueta superior'),
                        $this->area('operation.title', 'Título principal', 3)->required(),
                        $this->area('operation.description', 'Descripción', 4),
                    ]),
                Section::make('Tres etapas de funcionamiento')
                    ->description('Se conserva el orden 01 → 02 → 03 del diseño.')
                    ->schema([
                        Repeater::make('operation.steps')
                            ->label('Etapas')
                            ->schema([
                                $this->text('number', 'Número')->required(),
                                $this->text('title', 'Título')->required(),
                                $this->area('description', 'Descripción', 4),
                            ])
                            ->itemLabel(fn (array $state): ?string => ($state['number'] ?? '').' · '.($state['title'] ?? 'Etapa'))
                            ->collapsed()
                            ->minItems(3)
                            ->maxItems(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            'standards' => [
                Section::make('Encabezado de estándares')
                    ->schema([
                        $this->text('standards.eyebrow', 'Etiqueta superior'),
                        $this->area('standards.title', 'Título principal', 3)->required(),
                        $this->area('standards.description', 'Descripción', 4),
                    ]),
                Section::make('Cuatro estándares institucionales')
                    ->schema([
                        Repeater::make('standards.items')
                            ->label('Estándares')
                            ->schema([
                                $this->text('title', 'Título')->required(),
                                $this->area('description', 'Descripción', 4),
                                $this->image('icon', 'Icono'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Estándar')
                            ->collapsed()
                            ->minItems(4)
                            ->maxItems(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            'ecosystem' => [
                Section::make('Presentación del ecosistema')
                    ->schema([
                        $this->area('ecosystem.title', 'Título principal', 4)->required(),
                        $this->area('ecosystem.paragraph_one', 'Primer párrafo', 5),
                        $this->area('ecosystem.paragraph_two', 'Segundo párrafo', 5),
                        $this->image('ecosystem.background_image', 'Imagen de fondo'),
                    ]),
                Section::make('Enlace a Aura Capital')
                    ->schema([
                        $this->text('ecosystem.button_text', 'Texto del enlace'),
                        $this->text('ecosystem.button_url', 'URL del enlace')
                            ->helperText('Completar con la URL oficial confirmada.'),
                    ])
                    ->columns(2),
                Section::make('Componentes del ecosistema')
                    ->schema([
                        $this->area('ecosystem.components_title', 'Título del recuadro', 3),
                        Repeater::make('ecosystem.components')
                            ->label('Tres componentes')
                            ->schema([
                                $this->text('title', 'Nombre')->required(),
                                $this->area('description', 'Descripción', 3),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Componente')
                            ->collapsed()
                            ->minItems(3)
                            ->maxItems(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            'next_step' => [
                Section::make('Contenido del cierre')
                    ->schema([
                        $this->text('next_step.eyebrow', 'Etiqueta superior'),
                        $this->area('next_step.title', 'Título principal', 3)->required(),
                        $this->area('next_step.description', 'Descripción', 4),
                    ]),
                Section::make('Botones del cierre')
                    ->schema([
                        $this->text('next_step.button_primary_text', 'Texto del primer botón'),
                        $this->text('next_step.button_primary_url', 'Enlace del primer botón'),
                        $this->text('next_step.button_secondary_text', 'Texto del segundo botón'),
                        $this->text('next_step.button_secondary_url', 'Enlace del segundo botón'),
                    ])
                    ->columns(2),
            ],

            'seo' => SeoFields::schema('platform'),

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

    private function image(string $name, string $label, string $directory = 'gdi/plataforma'): FileUpload
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
                    'page' => 'platform',
                    'section' => $section,
                ]);

                $previous = $record->content ?? [];
                $submitted = $state[$section] ?? [];

                $record->content = array_replace(
                    is_array($previous) ? $previous : [],
                    is_array($submitted) ? $submitted : []
                );

                $record->save();
            }
        });

        Notification::make()
            ->title('Plataforma actualizada correctamente')
            ->success()
            ->send();
    }

    protected function previewPath(?string $statePath = null): string
    {
        return app(SiteSeo::class)->path('platform');
    }
}
