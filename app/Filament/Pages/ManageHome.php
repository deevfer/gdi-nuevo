<?php

namespace App\Filament\Pages;

use App\Models\PageSection;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
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

class ManageHome extends Page
{
    protected string $view = 'filament.pages.manage-home';

    protected static ?string $title = 'Administrar Inicio';
    protected static ?string $navigationLabel = 'Inicio';
    protected static ?string $slug = 'manage-home';

    public ?array $data = [];

    private const SECTIONS = [
        'hero' => '1. Portada',
        'infrastructure' => '2. Infraestructura',
        'operating_model' => '3. Modelo operativo',
        'audiences' => '4. Acceso diferenciado',
        'platform_access' => '5. Acceso a plataforma',
        'seo' => '6. SEO',
    ];

    public function mount(): void
    {
        $records = PageSection::query()
            ->where('page', 'home')
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
                    Tabs::make('Secciones de Inicio')
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

            // 1. PORTADA
            'hero' => [
                Section::make('Título y presentación')
                    ->description('El título se edita en un único campo. El diseño y el color destacado se aplicarán desde Vue.')
                    ->schema([
                        $this->area('hero.title', 'Título principal', 3)->required(),
                        $this->area('hero.description', 'Descripción de la portada', 4),
                    ]),

                Section::make('Imágenes de portada')
                    ->schema([
                        $this->image('hero.image_desktop', 'Imagen de escritorio'),
                        $this->image('hero.image_mobile', 'Imagen móvil'),
                    ])
                    ->columns(2),

                Section::make('Botones de portada')
                    ->schema([
                        $this->text('hero.button_primary_text', 'Texto del botón para inversionistas'),
                        $this->text('hero.button_primary_url', 'Enlace del botón para inversionistas'),
                        $this->text('hero.button_secondary_text', 'Texto del botón para empresas'),
                        $this->text('hero.button_secondary_url', 'Enlace del botón para empresas'),
                    ])
                    ->columns(2),
            ],

            // 2. INFRAESTRUCTURA
            'infrastructure' => [
                Section::make('Encabezado de infraestructura')
                    ->schema([
                        $this->text('infrastructure.eyebrow', 'Etiqueta superior'),
                        $this->area('infrastructure.title', 'Título principal', 3)->required(),
                        $this->area('infrastructure.description', 'Descripción', 4),
                    ]),

                Section::make('Diagrama: Capital')
                    ->schema([
                        $this->text('infrastructure.diagram.capital_title', 'Título de la columna'),
                        TagsInput::make('infrastructure.diagram.capital_items')
                            ->label('Elementos de capital')
                            ->helperText('Cada etiqueta corresponde a un elemento del diagrama.'),
                    ]),

                Section::make('Diagrama: Plataforma central')
                    ->schema([
                        $this->text('infrastructure.diagram.center_title', 'Título central'),
                        TagsInput::make('infrastructure.diagram.center_items')
                            ->label('Funciones de la plataforma'),
                    ]),

                Section::make('Diagrama: Soluciones')
                    ->schema([
                        $this->text('infrastructure.diagram.solutions_title', 'Título de la columna'),
                        TagsInput::make('infrastructure.diagram.solutions_items')
                            ->label('Elementos de soluciones'),
                    ]),

                Section::make('Texto inferior del diagrama')
                    ->schema([
                        $this->text('infrastructure.diagram.footer_title', 'Título inferior'),
                        $this->area('infrastructure.diagram.footer_description', 'Descripción inferior', 4),
                    ]),
            ],

            // 3. MODELO OPERATIVO
            'operating_model' => [
                Section::make('Encabezado del modelo operativo')
                    ->schema([
                        $this->text('operating_model.eyebrow', 'Etiqueta superior'),
                        $this->area('operating_model.title', 'Título principal', 3)->required(),
                        $this->area('operating_model.description', 'Descripción', 4),
                    ]),

                Section::make('Tres tarjetas del modelo')
                    ->description('Se mantienen las tres tarjetas y su orden para respetar el diseño de Figma.')
                    ->schema([
                        Repeater::make('operating_model.cards')
                            ->label('Tarjetas')
                            ->schema([
                                $this->text('number', 'Número')->required(),
                                $this->text('title', 'Título')->required(),
                                $this->area('description', 'Descripción', 4),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Tarjeta')
                            ->collapsed()
                            ->minItems(3)
                            ->maxItems(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            // 4. ACCESO DIFERENCIADO
            'audiences' => [
                Section::make('Encabezado de acceso diferenciado')
                    ->schema([
                        $this->text('audiences.eyebrow', 'Etiqueta superior'),
                        $this->area('audiences.title', 'Título principal', 3)->required(),
                        $this->area('audiences.description', 'Descripción', 4),
                    ]),

                Section::make('Tarjetas de perfiles')
                    ->description('Se mantienen las dos tarjetas del diseño: Inversionistas y Empresas.')
                    ->schema([
                        Repeater::make('audiences.cards')
                            ->label('Perfiles')
                            ->schema([
                                $this->text('title', 'Título del perfil')->required(),
                                $this->area('description', 'Descripción principal', 4),
                                $this->area('secondary_description', 'Descripción adicional', 4),
                                $this->image('image', 'Imagen de fondo'),
                                $this->text('button_text', 'Texto del enlace'),
                                $this->text('button_url', 'URL del enlace'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Perfil')
                            ->collapsed()
                            ->minItems(2)
                            ->maxItems(2)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ],

            // 5. ACCESO A LA PLATAFORMA
            'platform_access' => [
                Section::make('Contenido de la sección')
                    ->schema([
                        $this->text('platform_access.eyebrow', 'Etiqueta superior'),
                        $this->area('platform_access.title', 'Título principal', 3)->required(),
                        $this->area('platform_access.description', 'Descripción', 4),
                    ]),

                Section::make('Imagen de fondo')
                    ->description('Se utilizará la imagen original del diseño hasta que cargues una nueva desde el CMS.')
                    ->schema([
                        $this->image('platform_access.image', 'Imagen de fondo de Acceso a la plataforma'),
                    ]),

                Section::make('Botón de contacto')
                    ->schema([
                        $this->text('platform_access.button_text', 'Texto del botón'),
                        $this->text('platform_access.button_url', 'Enlace del botón'),
                    ])
                    ->columns(2),
            ],

            // 6. SEO
            'seo' => [
                Section::make('SEO básico')
                    ->description('Metadatos que se utilizarán en el HTML de la página de Inicio.')
                    ->schema([
                        $this->text('seo.meta_title', 'Metatítulo')
                            ->maxLength(255)
                            ->helperText('Como referencia, aproximadamente 50 a 60 caracteres.'),
                        $this->area('seo.meta_description', 'Metadescripción', 4)
                            ->helperText('Como referencia, aproximadamente 140 a 160 caracteres.'),
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

    private function image(string $name, string $label, string $directory = 'gdi/home'): FileUpload
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
                    'page' => 'home',
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
            ->title('Inicio actualizado correctamente')
            ->success()
            ->send();
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
