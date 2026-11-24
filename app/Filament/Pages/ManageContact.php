<?php

namespace App\Filament\Pages;

use App\Filament\Support\ManagePageSections;
use BackedEnum;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ManageContact extends ManagePageSections
{
    protected static ?string $title = 'Administrar Contacto';

    protected static ?string $navigationLabel = 'Contacto';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?int $navigationSort = 7;

    protected static ?string $slug = 'manage-contact';

    protected function pageKey(): string
    {
        return 'contact';
    }

    protected function imageDirectory(): string
    {
        return 'gdi/contacto';
    }

    protected function sections(): array
    {
        return [
            'hero' => '1. Portada',
            'form' => '2. Formulario',
            'info' => '3. Información de contacto',
            'areas' => '4. Áreas de contacto',
            'legal' => '5. Aviso legal',
            'seo' => '6. SEO',
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

            'form' => [
                Section::make('Encabezado del formulario')
                    ->schema([
                        $this->text('form.eyebrow', 'Etiqueta superior'),
                        $this->text('form.title', 'Título')->required(),
                        $this->text('form.submit_text', 'Texto del botón'),
                        $this->area('form.success_message', 'Mensaje de confirmación', 2),
                    ]),
                Section::make('Opciones de asunto')
                    ->schema([
                        $this->openRepeater('form.subjects', 'Asuntos', [
                            $this->text('name', 'Asunto')->required(),
                        ], 'name'),
                    ]),
                Section::make('Avisos por email')
                    ->description('Cada mensaje se guarda en Contactos › Mensajes de contacto. Los correos que reciben el aviso y la cuenta de envío se configuran en Configuración › Configuración del sitio › Correos.'),
            ],

            'info' => [
                Section::make('Encabezado')
                    ->schema([
                        $this->text('info.eyebrow', 'Etiqueta superior'),
                        $this->area('info.title', 'Título', 2)->required(),
                    ]),
                Section::make('Datos de contacto')
                    ->schema([
                        $this->text('info.office_title', 'Título de oficinas'),
                        $this->area('info.office_text', 'Dirección', 3),
                        $this->text('info.phone_title', 'Título de teléfono'),
                        $this->text('info.phone', 'Teléfono'),
                        $this->text('info.email_title', 'Título de email'),
                        $this->text('info.email', 'Email')->email(),
                    ])
                    ->columns(2),
                Section::make('Horario')
                    ->schema([
                        $this->text('info.hours_title', 'Título'),
                        $this->text('info.hours_text', 'Horario'),
                    ])
                    ->columns(2),
            ],

            'areas' => [
                $this->heading('areas', 'Encabezado', withDescription: false),
                Section::make('Tres áreas')
                    ->schema([
                        $this->fixedRepeater('areas.cards', 'Áreas', [
                            $this->text('title', 'Título')->required(),
                            $this->area('description', 'Descripción', 3),
                            $this->text('email', 'Email')->email(),
                        ], 3),
                    ]),
            ],

            'legal' => [
                Section::make('Aviso legal')
                    ->schema([
                        $this->text('legal.title', 'Título'),
                        $this->area('legal.text', 'Texto', 6),
                    ]),
            ],

            'seo' => $this->seoSchema(),

            default => [],
        };
    }
}
