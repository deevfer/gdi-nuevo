<?php

namespace App\Filament\Resources\Insights\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class InsightsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->wrap()
                    ->limit(80),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Publicado'),
                TernaryFilter::make('is_featured')->label('Destacado'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
