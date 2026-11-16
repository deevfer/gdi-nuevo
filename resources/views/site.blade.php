
@php
    $seo = $seo ?? [];

    // SEO principal
    $metaTitle = trim($seo['meta_title'] ?? '')
        ?: 'GDI Valores | Inicio';

    $metaDescription = trim($seo['meta_description'] ?? '')
        ?: '';

    // Open Graph: utiliza los valores SEO si no hay
    // contenido específico para redes sociales.
    $ogTitle = trim($seo['og_title'] ?? '')
        ?: $metaTitle;

    $ogDescription = trim($seo['og_description'] ?? '')
        ?: $metaDescription;

    // Imagen subida desde Filament al disco public.
    $ogImage = $seo['og_image'] ?? null;

    $ogImageUrl = $ogImage
        ? asset('storage/' . ltrim($ogImage, '/'))
        : null;

    // URL actual sin parámetros de consulta.
    $canonicalUrl = url()->current();
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>{{ $metaTitle }}</title>

    <!-- SEO básico -->
    <meta name="description"
          content="{{ $metaDescription }}">

    <link rel="canonical"
          href="{{ $canonicalUrl }}">

    <!-- Open Graph / LinkedIn -->
    <meta property="og:type"
          content="website">

    <meta property="og:title"
          content="{{ $ogTitle }}">

    <meta property="og:description"
          content="{{ $ogDescription }}">

    <meta property="og:url"
          content="{{ $canonicalUrl }}">

    <meta property="og:site_name"
          content="GDI Valores">

    @if($ogImageUrl)
        <meta property="og:image"
              content="{{ $ogImageUrl }}">
    @endif

    @fonts

    <!-- Contenido del CMS para Vue -->
    <script>
        window.gdiHomeContent = {{
            \Illuminate\Support\Js::from($homeContent ?? [])
        }};
    </script>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>
<body>
    <div id="app"></div>
</body>
</html>
