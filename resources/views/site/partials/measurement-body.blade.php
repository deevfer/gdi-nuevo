{{-- Aviso de cookies (textos editables en Páginas del sitio › Menú y pie de página) y scripts al final del body. --}}
@php
    $measurement = app(\App\Services\MeasurementScripts::class);
    $cookies = array_replace([
        'trigger' => 'Cookies',
        'title' => 'Usamos cookies',
        'body' => 'Usamos cookies esenciales para que el sitio funcione y, con su permiso, cookies de analítica y marketing para medir visitas y mejorar nuestras campañas.',
        'essential_title' => 'Esenciales',
        'essential_body' => 'Necesarias para el funcionamiento del sitio. Siempre activas.',
        'analytics_title' => 'Analítica',
        'analytics_body' => 'Nos ayudan a entender cómo se usa el sitio.',
        'marketing_title' => 'Marketing',
        'marketing_body' => 'Permiten medir campañas en Google, Meta y LinkedIn.',
        'accept' => 'Aceptar todas',
        'reject' => 'Solo esenciales',
        'configure' => 'Configurar',
        'save' => 'Guardar preferencias',
    ], array_filter($sharedContent['cookies'] ?? [], fn ($value) => filled($value)));
@endphp
<link rel="stylesheet" href="{{ asset('css/cookies.css').'?v='.filemtime(public_path('css/cookies.css')) }}">
<button id="cookie-trigger" class="cookie-trigger" type="button" data-cookie-open aria-controls="cookie-panel">{{ $cookies['trigger'] }}</button>
<section id="cookie-panel" class="cookie-panel" role="dialog" aria-labelledby="cookie-title" aria-describedby="cookie-description" hidden>
    <div class="cookie-heading"><h2 id="cookie-title">{{ $cookies['title'] }}</h2><button type="button" data-cookie-close aria-label="Cerrar">×</button></div>
    <p id="cookie-description">{{ $cookies['body'] }}</p>
    <div class="cookie-options" data-cookie-options hidden>
        <label><span><strong>{{ $cookies['essential_title'] }}</strong><small>{{ $cookies['essential_body'] }}</small></span><input type="checkbox" checked disabled></label>
        <label><span><strong>{{ $cookies['analytics_title'] }}</strong><small>{{ $cookies['analytics_body'] }}</small></span><input type="checkbox" name="cookie-analytics"></label>
        <label><span><strong>{{ $cookies['marketing_title'] }}</strong><small>{{ $cookies['marketing_body'] }}</small></span><input type="checkbox" name="cookie-marketing"></label>
    </div>
    <div class="cookie-actions">
        <button class="cookie-accept" type="button" data-cookie-accept>{{ $cookies['accept'] }}</button>
        <button type="button" data-cookie-reject>{{ $cookies['reject'] }}</button>
        <button type="button" data-cookie-configure>{{ $cookies['configure'] }}</button>
        <button class="cookie-accept" type="button" data-cookie-save hidden>{{ $cookies['save'] }}</button>
    </div>
</section>
{!! $measurement->withConsent($measurement->withoutDuplicates($siteSettings->custom_body_scripts, $siteSettings)) !!}
@if ($siteSettings->linkedInConfiguration()['enabled'])
    <script>window.GDI_LINKEDIN = @js($siteSettings->linkedInConfiguration());</script>
    <script type="text/plain" data-gdi-consent="marketing" defer data-src="{{ asset('js/tracking-linkedin.js').'?v='.filemtime(public_path('js/tracking-linkedin.js')) }}"></script>
@endif
