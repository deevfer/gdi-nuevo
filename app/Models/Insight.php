<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Insight extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'image',
        'is_featured',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
        'og_image_alt',
        'robots_index',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'date',
            'robots_index' => 'boolean',
        ];
    }

    /** Publicados con fecha de hoy o anterior. */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(fn (Builder $q) => $q
                ->whereNull('published_at')
                ->orWhereDate('published_at', '<=', Carbon::today()));
    }

    /** Datos que recibe el frontend en Vue. */
    public function toCard(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => route('insights.show', $this->slug),
            'category' => $this->category,
            'excerpt' => $this->excerpt,
            'image' => $this->image,
            'date' => $this->published_at
                ? ucfirst($this->published_at->locale('es')->translatedFormat('F Y'))
                : null,
            'date_iso' => $this->published_at?->toDateString(),
        ];
    }
}
