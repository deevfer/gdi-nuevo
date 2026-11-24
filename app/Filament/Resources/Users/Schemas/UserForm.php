<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del usuario')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Text::make(fn (?User $record): string => 'Rol: '.($record?->role ?? UserRole::Webmaster)->label()
                            .($record?->isSuperAdmin() ? '. El super admin es único y no se puede eliminar.' : '. Puede editar el contenido del CMS y su propio perfil, pero no administra usuarios.'))
                            ->columnSpanFull(),
                    ]),
                Section::make('Contraseña')
                    ->description(fn (string $operation): string => $operation === 'create'
                        ? 'Comparta la contraseña con la persona; podrá cambiarla desde "Perfil" en el menú de su cuenta.'
                        : 'Déjela vacía para conservar la contraseña actual.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->rule(Password::min(8))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->same('password_confirmation')
                            ->autocomplete('new-password'),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(false)
                            ->autocomplete('new-password'),
                    ]),
            ]);
    }
}
