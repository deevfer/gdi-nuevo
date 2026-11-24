<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')
                    ->label('Leído')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => $record->read_at !== null),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->weight(fn ($record) => $record->read_at ? null : 'bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('company')
                    ->label('Empresa')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('subject')
                    ->label('Asunto')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Leído')
                    ->nullable(),
                SelectFilter::make('subject')
                    ->label('Asunto')
                    ->options(fn (): array => ContactMessage::query()
                        ->whereNotNull('subject')
                        ->distinct()
                        ->orderBy('subject')
                        ->pluck('subject', 'subject')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
