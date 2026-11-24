{{-- Metadatos SEO y para redes sociales. Los genera App\Services\SiteSeo. --}}
<title>{{ $meta['title'] }}</title>
<meta name="description" content="{{ $meta['description'] }}">
<meta name="robots" content="{{ $meta['robots'] }}">
<link rel="canonical" href="{{ $meta['canonical'] }}">
<meta property="og:site_name" content="GDI Valores">
<meta property="og:locale" content="es_GT">
<meta property="og:type" content="{{ $meta['type'] }}">
<meta property="og:title" content="{{ $meta['og_title'] }}">
<meta property="og:description" content="{{ $meta['og_description'] }}">
<meta property="og:url" content="{{ $meta['canonical'] }}">
<meta property="og:image" content="{{ $meta['image'] }}">
<meta property="og:image:alt" content="{{ $meta['image_alt'] }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['og_title'] }}">
<meta name="twitter:description" content="{{ $meta['og_description'] }}">
<meta name="twitter:image" content="{{ $meta['image'] }}">
@if ($meta['published'])<meta property="article:published_time" content="{{ $meta['published'] }}">@endif
@if ($meta['modified'])<meta property="article:modified_time" content="{{ $meta['modified'] }}">@endif
@if (filled($meta['verification']['search_console_verification']))<meta name="google-site-verification" content="{{ $meta['verification']['search_console_verification'] }}">@endif
@if (filled($meta['verification']['bing_verification']))<meta name="msvalidate.01" content="{{ $meta['verification']['bing_verification'] }}">@endif
@if (filled($meta['verification']['facebook_domain_verification']))<meta name="facebook-domain-verification" content="{{ $meta['verification']['facebook_domain_verification'] }}">@endif
@foreach (app(\App\Services\SiteSeo::class)->customVerificationTags() as $tag){!! $tag !!}@endforeach
<script type="application/ld+json">{!! json_encode($meta['schema'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
