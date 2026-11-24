<?php

namespace App\Services;

use App\Models\Insight;
use App\Models\PageSection;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Metadatos SEO, datos estructurados (schema.org) y sitemap del sitio público.
 */
class SiteSeo
{
    /** Páginas del sitio: clave en page_sections => ruta, nombre visible y título por defecto. */
    public const PAGES = [
        'home' => ['route' => 'home', 'name' => 'Inicio', 'title' => 'GDI Valores | Inicio'],
        'platform' => ['route' => 'platform', 'name' => 'Plataforma', 'title' => 'Plataforma | GDI Valores'],
        'financing' => ['route' => 'financing', 'name' => 'Financiamiento estructurado', 'title' => 'Financiamiento estructurado | GDI Valores'],
        'investors' => ['route' => 'investors', 'name' => 'Inversionistas', 'title' => 'Inversionistas | GDI Valores'],
        'strategies' => ['route' => 'strategies', 'name' => 'Estrategias de inversión', 'title' => 'Estrategias de inversión | GDI Valores'],
        'insights' => ['route' => 'insights', 'name' => 'Insights', 'title' => 'Insights | GDI Valores'],
        'contact' => ['route' => 'contact', 'name' => 'Contacto', 'title' => 'Contacto | GDI Valores'],
    ];

    /** @return array<string, mixed> */
    public function settings(): array
    {
        $saved = Schema::hasTable('seo_settings') ? SeoSetting::find(1)?->data ?? [] : [];

        return array_replace([
            'public_url' => config('seo.public_url'),
            'indexing_enabled' => (bool) config('seo.indexing_enabled'),
            'default_image' => '/images/gdi/plataforma/hero.png',
            'search_console_verification' => '',
            'bing_verification' => '',
            'facebook_domain_verification' => '',
            'custom_verification_tags' => '',
            'same_as' => [],
            'llms_enabled' => true,
        ], $saved);
    }

    public function origin(): string
    {
        return rtrim((string) $this->settings()['public_url'], '/');
    }

    public function absolute(string $path): string
    {
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        return $this->origin().'/'.ltrim($path, '/');
    }

    /** Fuera de producción el sitio nunca se indexa, aunque el CMS lo permita. */
    public function publicIndexingEnabled(): bool
    {
        return app()->environment('production') && (bool) $this->settings()['indexing_enabled'];
    }

    public function path(string $page): string
    {
        return route(self::PAGES[$page]['route'], absolute: false);
    }

    /** Ruta pública de una imagen guardada por el CMS (disco public) o de un archivo de /public. */
    public function imagePath(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (preg_match('~^https?://~i', $value) || str_starts_with($value, '/')) {
            return $value;
        }

        return '/storage/'.ltrim(preg_replace('~^storage/~', '', $value), '/');
    }

    /** Solo las etiquetas <meta> del código de verificación pegado en el CMS. @return array<int, string> */
    public function customVerificationTags(): array
    {
        preg_match_all('~<meta\s[^<>]*?/?>~i', (string) ($this->settings()['custom_verification_tags'] ?? ''), $tags);

        return array_values(array_filter($tags[0], fn (string $tag): bool => ! preg_match('~\son\w+\s*=|javascript:~i', $tag)));
    }

    /**
     * Metadatos de una página del sitio o de un artículo de Insights.
     *
     * @param  array<string, mixed>  $sections  Contenido de la página (page_sections).
     * @return array<string, mixed>
     */
    public function metadata(string $page, array $sections, ?Insight $article = null): array
    {
        $settings = $this->settings();
        $cms = $article ? [] : ($sections['seo'] ?? []);
        $hero = $sections['hero'] ?? [];

        if ($article) {
            $path = route('insights.show', $article->slug, false);
            $title = ($article->meta_title ?: $article->title).' | GDI Valores';
            $description = $article->meta_description ?: ($article->excerpt ?: strip_tags((string) $article->body));
            $image = $this->imagePath($article->image);
            $imageAlt = $article->og_image_alt ?: $article->title;
            $indexable = $article->robots_index !== false;
            $follow = true;
        } else {
            $path = $this->path($page);
            $title = trim((string) ($cms['meta_title'] ?? '')) ?: self::PAGES[$page]['title'];
            $description = trim((string) ($cms['meta_description'] ?? '')) ?: (string) ($hero['description'] ?? '');
            $image = $this->imagePath($cms['og_image'] ?? null) ?: $this->imagePath($hero['image_desktop'] ?? null);
            $imageAlt = trim((string) ($cms['og_image_alt'] ?? '')) ?: 'GDI Valores';
            $indexable = ($cms['robots_index'] ?? true) !== false;
            $follow = ($cms['robots_follow'] ?? true) !== false;
        }

        $description = $this->plain($description, 170);
        $customCanonical = trim((string) ($cms['canonical_url'] ?? ''));
        $canonical = filter_var($customCanonical, FILTER_VALIDATE_URL) && preg_match('~^https?://~i', $customCanonical)
            ? $customCanonical
            : $this->absolute($path);
        $imageUrl = $this->absolute($image ?: $settings['default_image']);
        $robots = ($this->publicIndexingEnabled() && $indexable ? 'index' : 'noindex')
            .', '.($follow ? 'follow' : 'nofollow')
            .($this->publicIndexingEnabled() && $indexable ? ', max-image-preview:large' : '');

        return [
            'title' => $title,
            'description' => $description,
            'og_title' => trim((string) ($cms['og_title'] ?? '')) ?: $title,
            'og_description' => $this->plain((string) (($cms['og_description'] ?? '') ?: $description), 200),
            'canonical' => $canonical,
            'robots' => $robots,
            'image' => $imageUrl,
            'image_alt' => $imageAlt,
            'type' => $article ? 'article' : 'website',
            'published' => $article?->published_at?->toIso8601String(),
            'modified' => $article?->updated_at?->toIso8601String(),
            'verification' => $settings,
            'schema' => ['@context' => 'https://schema.org', '@graph' => $this->graph($page, $sections, $article, $title, $description, $canonical, $imageUrl)],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function graph(string $page, array $sections, ?Insight $article, string $title, string $description, string $canonical, string $image): array
    {
        $organization = $this->organization();
        $graph = [$organization];

        if ($page === 'home') {
            $graph[] = ['@type' => 'WebSite', '@id' => $this->origin().'/#website', 'url' => $this->origin().'/', 'name' => 'GDI Valores', 'inLanguage' => 'es', 'publisher' => ['@id' => $organization['@id']]];
        }

        $graph[] = [
            '@type' => $page === 'insights' && ! $article ? 'CollectionPage' : 'WebPage',
            '@id' => $canonical.'#webpage',
            'url' => $canonical,
            'name' => $title,
            'description' => $description,
            'inLanguage' => 'es',
            'isPartOf' => ['@id' => $this->origin().'/#website'],
            'publisher' => ['@id' => $organization['@id']],
        ];

        if ($article) {
            $graph[] = [
                '@type' => 'Article',
                'headline' => $article->title,
                'description' => $description,
                'url' => $canonical,
                'image' => $image,
                'inLanguage' => 'es',
                'articleSection' => $article->category,
                'mainEntityOfPage' => ['@id' => $canonical.'#webpage'],
                'author' => ['@id' => $organization['@id']],
                'publisher' => ['@id' => $organization['@id']],
                'datePublished' => $article->published_at?->toIso8601String(),
                'dateModified' => $article->updated_at?->toIso8601String(),
            ];
        }

        // Preguntas frecuentes de los fondos: solo las que tienen respuesta.
        $faqs = collect($sections['funds']['items'] ?? [])
            ->flatMap(fn (array $fund): array => $fund['faqs'] ?? [])
            ->filter(fn (mixed $faq): bool => is_array($faq) && filled($faq['question'] ?? null) && filled($faq['answer'] ?? null))
            ->unique('question')
            ->values();
        if ($faqs->isNotEmpty()) {
            $graph[] = [
                '@type' => 'FAQPage',
                '@id' => $canonical.'#faq',
                'mainEntity' => $faqs->map(fn (array $faq): array => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $this->plain($faq['answer'], 1000)],
                ])->all(),
            ];
        }

        if ($page !== 'home') {
            $crumbs = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => $this->absolute('/')]];
            if ($article) {
                $crumbs[] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'Insights', 'item' => $this->absolute($this->path('insights'))];
            }
            $crumbs[] = ['@type' => 'ListItem', 'position' => count($crumbs) + 1, 'name' => $article?->title ?? self::PAGES[$page]['name'], 'item' => $canonical];
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => $crumbs];
        }

        return $graph;
    }

    /** Nodo único de la organización, con los datos de contacto que se editan en la página Contacto. @return array<string, mixed> */
    public function organization(): array
    {
        $info = Schema::hasTable('page_sections')
            ? PageSection::query()->where('page', 'contact')->where('section', 'info')->value('content') ?? []
            : [];
        $sameAs = array_values(array_filter((array) ($this->settings()['same_as'] ?? []), fn (mixed $url): bool => is_string($url) && preg_match('~^https://\S+$~i', $url) === 1));

        return array_filter([
            '@type' => 'FinancialService',
            '@id' => $this->origin().'/#organization',
            'name' => 'GDI Valores',
            'url' => $this->origin().'/',
            'logo' => $this->absolute('/images/gdi/marca/logo.svg'),
            'image' => $this->absolute($this->settings()['default_image']),
            'description' => 'Plataforma institucional regulada para la administración de capital y la estructuración de financiamiento en el mercado de valores.',
            'telephone' => $info['phone'] ?? null,
            'email' => $info['email'] ?? null,
            'address' => filled($info['office_text'] ?? null) ? ['@type' => 'PostalAddress', 'streetAddress' => $info['office_text'], 'addressLocality' => 'Ciudad de Guatemala', 'addressCountry' => 'GT'] : null,
            'areaServed' => 'GT',
            'sameAs' => $sameAs ?: null,
        ]);
    }

    public function plain(string $value, int $limit = 170): string
    {
        $value = preg_replace('~<(?:br\b[^>]*|/(?:p|div|h[1-6]|li|blockquote))>~i', ' ', $value);
        $text = preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return Str::limit(trim((string) $text), $limit, '…');
    }

    /** Páginas que pueden indexarse, con su fecha de última modificación. @return array<int, array{path: string, lastmod: ?string}> */
    public function sitemapEntries(): array
    {
        $updated = PageSection::query()->selectRaw('page, max(updated_at) as updated')->groupBy('page')->pluck('updated', 'page');
        $entries = [];

        foreach (array_keys(self::PAGES) as $page) {
            $seo = PageSection::query()->where('page', $page)->where('section', 'seo')->value('content') ?? [];
            if (($seo['robots_index'] ?? true) === false) {
                continue;
            }
            $entries[] = ['path' => $this->path($page), 'lastmod' => isset($updated[$page]) ? date(DATE_ATOM, strtotime($updated[$page])) : null];
        }

        foreach (Insight::query()->published()->where('robots_index', true)->orderByDesc('published_at')->get() as $article) {
            $entries[] = ['path' => route('insights.show', $article->slug, false), 'lastmod' => $article->updated_at?->toIso8601String()];
        }

        return $entries;
    }
}
