<?php

namespace App\Filament\Resources\Insights;

use App\Filament\Resources\Insights\Pages\CreateInsight;
use App\Filament\Resources\Insights\Pages\EditInsight;
use App\Filament\Resources\Insights\Pages\ListInsights;
use App\Filament\Resources\Insights\Schemas\InsightForm;
use App\Filament\Resources\Insights\Tables\InsightsTable;
use App\Models\Insight;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InsightResource extends Resource
{
    protected static ?string $model = Insight::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $modelLabel = 'artículo';

    protected static ?string $pluralModelLabel = 'artículos de Insights';

    protected static ?string $navigationLabel = 'Artículos de Insights';

    protected static string|UnitEnum|null $navigationGroup = 'Insights';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return InsightForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InsightsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInsights::route('/'),
            'create' => CreateInsight::route('/create'),
            'edit' => EditInsight::route('/{record}/edit'),
        ];
    }
}
