<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Mensaje')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('subject')->label('Asunto')->placeholder('Sin asunto'),
                        TextEntry::make('message')->label('Mensaje')->prose(),
                    ]),
                Section::make('Remitente')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name')->label('Nombre'),
                        TextEntry::make('email')->label('Email')->copyable(),
                        TextEntry::make('company')->label('Empresa')->placeholder('—'),
                        TextEntry::make('phone')->label('Teléfono')->placeholder('—'),
                        TextEntry::make('created_at')->label('Recibido')->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
