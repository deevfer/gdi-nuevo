<?php

namespace App\Filament\Pages;

use App\Filament\Support\ManagePageSections;
use App\Services\EmailTexts;
use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Textos de los correos que envía el sitio, con vista previa del correo real. */
class ManageEmails extends ManagePageSections
{
    protected static ?string $title = 'Administrar textos de correos';

    protected static ?string $navigationLabel = 'Textos de correos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'manage-emails';

    /** Correo => ruta de su vista previa. */
    private const PREVIEWS = [
        'lead' => '/admin/correos/vista-previa/aviso-al-equipo',
        'confirmation' => '/admin/correos/vista-previa/confirmacion',
    ];

    protected function pageKey(): string
    {
        return 'emails';
    }

    protected function imageDirectory(): string
    {
        return 'gdi/correos';
    }

    protected function previewPath(?string $statePath = null): string
    {
        $email = preg_match('/^data\.([a-z_]+)\./', (string) $statePath, $match) ? $match[1] : 'lead';

        return self::PREVIEWS[$email] ?? self::PREVIEWS['lead'];
    }

    public function mount(): void
    {
        parent::mount();

        // Muestra los textos por defecto en los campos que todavía no se han editado.
        $state = [];
        foreach (array_keys(EmailTexts::DEFAULTS) as $email) {
            $state[$email] = app(EmailTexts::class)->get($email);
        }
        $this->form->fill($state);
    }

    protected function sections(): array
    {
        return [
            'lead' => '1. Aviso al equipo',
            'confirmation' => '2. Confirmación al cliente',
        ];
    }

    protected function sectionSchema(string $section): array
    {
        return match ($section) {
            'lead' => [
                Section::make('Aviso al equipo')
                    ->description('Llega a los correos del equipo (Configuración del sitio › Correos) cada vez que alguien envía el formulario de Contacto. Responder al aviso le escribe directamente a la persona.')
                    ->schema([
                        $this->text('lead.subject', 'Asunto')->helperText('Se agrega el asunto que eligió la persona, o su nombre.')->required(),
                        $this->text('lead.eyebrow', 'Etiqueta superior'),
                        $this->text('lead.title', 'Título')->required(),
                        $this->text('lead.intro', 'Texto después del nombre')->helperText('Ejemplo: "Ana López escribió desde el formulario de contacto del sitio."'),
                        $this->text('lead.cta', 'Texto del botón')->helperText('El botón abre el mensaje en el CMS.'),
                        $this->area('lead.reply_note', 'Nota final', 2),
                        $this->area('lead.footer', 'Texto del pie', 2),
                    ]),
            ],

            'confirmation' => [
                Section::make('Confirmación al cliente')
                    ->description('Le confirma a la persona que su mensaje llegó, con un resumen de lo que envió. Responder a este correo le escribe al primer correo del equipo.')
                    ->schema([
                        Toggle::make('confirmation.enabled')->label('Enviar confirmación a la persona que escribió'),
                        $this->text('confirmation.subject', 'Asunto')->required(),
                        $this->text('confirmation.preheader', 'Texto de vista previa en la bandeja')->helperText('Aparece junto al asunto en Gmail y Outlook.'),
                        $this->text('confirmation.eyebrow', 'Etiqueta superior'),
                        $this->text('confirmation.title', 'Título')->required(),
                        $this->text('confirmation.greeting', 'Saludo')->helperText('Se completa con el nombre: "Hola Ana López,".'),
                        $this->area('confirmation.body', 'Mensaje', 4),
                        $this->text('confirmation.summary_title', 'Título del resumen')->helperText('Vacío oculta el resumen del mensaje.'),
                        $this->area('confirmation.reply_note', 'Nota destacada', 2),
                        $this->text('confirmation.cta', 'Texto del botón')->helperText('Vacío oculta el botón.'),
                        $this->text('confirmation.cta_url', 'Enlace del botón')->helperText('Ruta del sitio (/estrategias-de-inversion) o URL completa.'),
                        $this->area('confirmation.signature', 'Firma', 2),
                        $this->area('confirmation.footer', 'Texto del pie', 2),
                    ]),
            ],

            default => [],
        };
    }
}
