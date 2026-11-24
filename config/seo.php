<?php

return [
    // Dominio público por defecto; se cambia desde Visibilidad › Configuración SEO.
    'public_url' => env('SEO_PUBLIC_URL', 'https://www.gdivalores.com'),
    'indexing_enabled' => env('SEO_INDEXING_ENABLED', true),
    'site_name' => 'GDI Valores',
    'sources' => [
        'Google Search Central' => 'https://developers.google.com/search/docs',
        'Schema.org' => 'https://schema.org/FinancialService',
        'Meta Pixel' => 'https://developers.facebook.com/docs/meta-pixel',
        'Google Consent Mode' => 'https://developers.google.com/tag-platform/security/guides/consent',
    ],
];
