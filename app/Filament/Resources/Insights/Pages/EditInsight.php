<?php

namespace App\Filament\Resources\Insights\Pages;

use App\Filament\Concerns\HasLivePreview;
use App\Filament\Resources\Insights\InsightResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInsight extends EditRecord
{
    use HasLivePreview;

    protected static string $resource = InsightResource::class;

    protected string $view = 'filament.resources.insights.edit-insight';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /** Guarda el borrador sin avisos y lo muestra aunque el artículo todavía no esté publicado. */
    protected function applyPreviewDraft(): void
    {
        $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

        $article = $this->getRecord();
        if (! $article->is_published || ! $article->published_at || $article->published_at->isFuture()) {
            $article->forceFill(['is_published' => true, 'published_at' => $article->published_at?->isPast() ? $article->published_at : now()])->saveQuietly();
        }
    }

    /** El borrador se revirtió: se recarga el registro para que Guardar compare con la base de datos. */
    protected function afterPreview(): void
    {
        $this->getRecord()->refresh();
    }

    protected function previewPath(?string $statePath = null): string
    {
        return '/insights/'.rawurlencode($this->getRecord()->slug);
    }
}
