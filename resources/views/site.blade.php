{{--
    Plantilla única del sitio público. El contenido lo pinta Vue (resources/js/app.js);
    los metadatos SEO, el Schema y las etiquetas de medición se generan aquí en el servidor.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicons')

    @include('site.partials.seo')
    @include('site.partials.measurement-head')

    @fonts
    <script>
        window.gdiPageName = {{ \Illuminate\Support\Js::from($pageName) }};
        window.gdiPageContent = {{ \Illuminate\Support\Js::from($pageContent) }};
        window.gdiSharedContent = {{ \Illuminate\Support\Js::from($sharedContent) }};
        window.gdiRecaptcha = {{ \Illuminate\Support\Js::from($pageName === 'contact' && $siteSettings->recaptchaIsConfigured() ? $siteSettings->recaptcha_site_key : null) }};
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
    @include('site.partials.measurement-body')
</body>
</html>
