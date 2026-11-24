<?php

namespace App\Filament\Concerns;

use App\Services\PagePreview;
use Filament\Support\Enums\Width;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Renderless;

/**
 * Vista previa de la página pública, al lado del formulario, con los cambios sin guardar.
 */
trait HasLivePreview
{
    /** Ruta que se muestra en la vista previa; $statePath es el campo que se está editando (p. ej. "data.lead.title"). */
    abstract protected function previewPath(?string $statePath = null): string;

    /** Escribe el borrador exactamente como lo haría Guardar; se revierte después de generar la vista. */
    protected function applyPreviewDraft(): void
    {
        $this->save();
    }

    protected function afterPreview(): void {}

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    /**
     * Renderless: el formulario no cambia, así Livewire no lo vuelve a pintar en cada actualización.
     *
     * @return array{html: string|null, status: int, path: string}
     */
    #[Renderless]
    public function renderPreview(?string $statePath = null): array
    {
        $path = '/';

        try {
            $result = app(PagePreview::class)->render(function () use ($statePath, &$path): string {
                $this->applyPreviewDraft();

                return $path = $this->previewPath($statePath);
            });
        } catch (ValidationException) {
            $result = ['html' => null, 'status' => 422];
        } finally {
            session()->forget('filament.notifications');
            $this->afterPreview();
        }

        return [...$result, 'path' => $path];
    }

    /** @return array{localized: bool, publicUrl: string} */
    public function previewConfig(): array
    {
        return [
            'localized' => false,
            'publicUrl' => url($this->previewPath()),
        ];
    }
}
