<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Abrir el mensaje lo marca como leído.
        if ($this->record->read_at === null) {
            $this->record->forceFill(['read_at' => now()])->save();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Responder por email')
                ->icon('heroicon-o-envelope')
                ->url(fn (): string => 'mailto:'.$this->record->email.'?subject='.rawurlencode('Re: '.($this->record->subject ?: 'Contacto GDI Valores'))),
            DeleteAction::make(),
        ];
    }
}
