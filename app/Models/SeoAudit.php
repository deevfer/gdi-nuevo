<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Resultado de cada análisis SEO del dashboard. */
class SeoAudit extends Model
{
    protected $fillable = ['score', 'page_count', 'report'];

    protected function casts(): array
    {
        return ['report' => 'array'];
    }
}
