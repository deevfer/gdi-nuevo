
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $seo['meta_title'] ?? 'GDI Valores | Inicio' }}</title>

    <meta name="description"
          content="{{ $seo['meta_description'] ?? '' }}">

    <meta property="og:title"
          content="{{ $seo['og_title'] ?: ($seo['meta_title'] ?? 'GDI Valores') }}">

    <meta property="og:description"
          content="{{ $seo['og_description'] ?: ($seo['meta_description'] ?? '') }}">

    <meta property="og:type" content="website">

    @fonts

    <script>
        window.gdiHomeContent = {{ \Illuminate\Support\Js::from($homeContent) }};
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
