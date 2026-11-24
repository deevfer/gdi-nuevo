<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Configuración SEO global (una sola fila con los datos en JSON). */
class SeoSetting extends Model
{
    protected $fillable = ['data'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }
}
