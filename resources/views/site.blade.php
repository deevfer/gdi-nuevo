@php
    $pageName = $pageName ?? 'home';
    $pageContent = $pageContent ?? [];
    $seo = $seo ?? [];

    $fallbackTitle = match ($pageName) {
        'platform' => 'Plataforma | GDI Valores',
        'financing' => 'Financiamiento estructurado | GDI Valores',
        default => 'GDI Valores | Inicio',
    };
    $metaTitle = trim((string) ($seo['meta_title'] ?? '')) ?: $fallbackTitle;
    $metaDescription = trim((string) ($seo['meta_description'] ?? ''));
    $ogTitle = trim((string) ($seo['og_title'] ?? '')) ?: $metaTitle;
    $ogDescription = trim((string) ($seo['og_description'] ?? '')) ?: $metaDescription;

    $customCanonical = trim((string) ($seo['canonical_url'] ?? ''));
    $canonicalUrl = filter_var($customCanonical, FILTER_VALIDATE_URL)
        ? $customCanonical
        : url()->current();

    $ogImage = trim((string) ($seo['og_image'] ?? ''));
    $ogImageUrl = $ogImage
        ? (preg_match('/^https?:\/\//i', $ogImage)
            ? $ogImage
            : asset('storage/' . ltrim(preg_replace('#^/?storage/#', '', $ogImage), '/')))
        : null;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="GDI Valores">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if ($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif

    @fonts
    <script>
        window.gdiPageName = {{ \Illuminate\Support\Js::from($pageName) }};
        window.gdiPageContent = {{ \Illuminate\Support\Js::from($pageContent) }};
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
