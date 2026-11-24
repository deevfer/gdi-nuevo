<?php

namespace App\Services;

use App\Filament\Pages\ManageContact;
use App\Filament\Pages\ManageFinancing;
use App\Filament\Pages\ManageHome;
use App\Filament\Pages\ManageInsights;
use App\Filament\Pages\ManageInvestors;
use App\Filament\Pages\ManagePlatform;
use App\Filament\Pages\ManageStrategies;
use App\Filament\Resources\Insights\InsightResource;
use App\Models\Insight;
use App\Models\PageSection;
use App\Models\SeoAudit;
use App\Models\SiteSetting;

/**
 * Análisis SEO interno del CMS: revisa la infraestructura y los metadatos de cada página y artículo.
 * La nota es propia de este CMS; no es una calificación de Google.
 */
class SeoAuditor
{
    private const EDITORS = [
        'home' => ManageHome::class,
        'platform' => ManagePlatform::class,
        'financing' => ManageFinancing::class,
        'investors' => ManageInvestors::class,
        'strategies' => ManageStrategies::class,
        'insights' => ManageInsights::class,
        'contact' => ManageContact::class,
    ];

    public function __construct(private SiteSeo $seo) {}

    public function run(): SeoAudit
    {
        $pages = [];

        foreach (SiteSeo::PAGES as $page => $info) {
            $sections = PageSection::query()->where('page', $page)->pluck('content', 'section')->map(fn ($content) => is_array($content) ? $content : (json_decode((string) $content, true) ?? []))->all();
            $pages[] = $this->inspect('page', $info['name'], $this->seo->path($page), $this->seo->metadata($page, $sections), ($sections['seo']['robots_index'] ?? true) !== false, self::EDITORS[$page]::getUrl());
        }

        foreach (Insight::query()->published()->orderByDesc('published_at')->get() as $article) {
            $pages[] = $this->inspect('post', $article->title, route('insights.show', $article->slug, false), $this->seo->metadata('insights', [], $article), $article->robots_index !== false, InsightResource::getUrl('edit', ['record' => $article]));
        }

        // Títulos repetidos confunden a los buscadores: se revisa al final, con todas las páginas.
        $counts = array_count_values(array_map(fn (array $page): string => $page['meta_title'], $pages));
        foreach ($pages as &$page) {
            $page['checks'][] = $this->check('unique', 'Título único', 10, $counts[$page['meta_title']] === 1, 'Use un metatítulo distinto en cada página y artículo.');
            $page['score'] = max(1, array_sum(array_column($page['checks'], 'earned')));
        }
        unset($page);

        $global = $this->globalChecks();
        $site = array_sum(array_column($global, 'earned'));
        $corporate = collect($pages)->where('kind', 'page')->avg('score') ?? 0;
        $articles = collect($pages)->where('kind', 'post')->avg('score');
        $score = max(1, (int) round($site * .4 + $corporate * ($articles === null ? .6 : .35) + ($articles ?? 0) * .25));

        $issues = [];
        foreach ($pages as $page) {
            foreach ($page['checks'] as $check) {
                if (! $check['passed']) {
                    $issues[$check['key']] ??= ['label' => $check['label'], 'help' => $check['help'], 'count' => 0, 'weight' => $check['weight']];
                    $issues[$check['key']]['count']++;
                }
            }
        }
        usort($pages, fn (array $a, array $b): int => $a['score'] <=> $b['score']);
        uasort($issues, fn (array $a, array $b): int => ($b['weight'] * $b['count']) <=> ($a['weight'] * $a['count']));

        $report = [
            'version' => 1,
            'score' => $score,
            'site_score' => $site,
            'pages_score' => (int) floor($corporate),
            'articles_score' => $articles === null ? null : (int) floor($articles),
            'checked_at' => now()->toIso8601String(),
            'origin' => $this->seo->origin(),
            'environment' => app()->environment(),
            'global' => $global,
            'pages' => $pages,
            'issues' => array_values($issues),
        ];

        $audit = SeoAudit::create(['score' => $score, 'page_count' => count($pages), 'report' => $report]);
        SeoAudit::query()->whereNotIn('id', SeoAudit::query()->latest('id')->limit(20)->pluck('id'))->delete();

        return $audit;
    }

    /** @param array<string, mixed> $meta @return array<string, mixed> */
    private function inspect(string $kind, string $name, string $path, array $meta, bool $indexable, string $editUrl): array
    {
        $titleLength = mb_strlen($meta['title']);
        $descriptionLength = mb_strlen($meta['description']);
        $types = array_column($meta['schema']['@graph'] ?? [], '@type');

        return [
            'kind' => $kind,
            'title' => $name,
            'meta_title' => $meta['title'],
            'path' => $path,
            'edit_url' => $editUrl,
            'score' => 1,
            'checks' => [
                $this->check('title', 'Metatítulo de 30 a 60 caracteres', 20, $titleLength >= 30 && $titleLength <= 60, "Tiene {$titleLength} caracteres. Entre 30 y 60 se muestra completo en Google."),
                $this->check('description', 'Metadescripción de 70 a 160 caracteres', 20, $descriptionLength >= 70 && $descriptionLength <= 160, "Tiene {$descriptionLength} caracteres. Una descripción de 70 a 160 caracteres resume la página en el resultado de búsqueda."),
                $this->check('canonical', 'URL canónica en el dominio público', 10, str_starts_with($meta['canonical'], 'https://'), 'La URL canónica debe usar el dominio público con HTTPS (Visibilidad › Configuración SEO).'),
                $this->check('image', 'Imagen para compartir', 15, $this->imageExists($meta['image']), 'Suba una imagen de 1200 × 630 px en la pestaña SEO; es la que aparece al compartir en LinkedIn y WhatsApp.'),
                $this->check('image_alt', 'Texto alternativo de la imagen para compartir', 5, filled($meta['image_alt']) && $meta['image_alt'] !== 'GDI Valores', 'Describa la imagen para compartir en la pestaña SEO.'),
                $this->check('indexable', 'Permite la indexación', 10, $indexable, 'La indexación está apagada en la pestaña SEO: la página no aparecerá en buscadores.'),
                $this->check('schema', 'Datos estructurados (schema.org)', 10, in_array('WebPage', $types, true) || in_array('CollectionPage', $types, true), 'La página debe publicar datos estructurados WebPage.'),
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function globalChecks(): array
    {
        $settings = $this->seo->settings();
        $site = SiteSetting::current();
        $contact = PageSection::query()->where('page', 'contact')->where('section', 'info')->value('content') ?? [];

        return [
            $this->check('domain', 'Dominio público con HTTPS', 15, str_starts_with($this->seo->origin(), 'https://'), 'Configure el dominio público en Visibilidad › Configuración SEO.'),
            $this->check('indexing', 'Indexación activa en producción', 15, $this->seo->publicIndexingEnabled(), app()->environment('production') ? 'Active "Permitir indexación en producción" en Configuración SEO.' : 'Este entorno no es producción: el sitio pide no indexar a propósito. Se aprueba al publicar con APP_ENV=production.'),
            $this->check('sitemap', 'Sitemap con páginas', 10, count($this->seo->sitemapEntries()) > 0, 'El sitemap /sitemap.xml debe listar las páginas que pueden indexarse.'),
            $this->check('search_console', 'Google Search Console verificado', 15, filled($settings['search_console_verification']), 'Pegue el código de verificación de Google Search Console en Configuración SEO.'),
            $this->check('bing', 'Bing Webmaster Tools verificado', 5, filled($settings['bing_verification']), 'Pegue el código de verificación de Bing en Configuración SEO.'),
            $this->check('default_image', 'Imagen para compartir por defecto', 10, $this->imageExists($this->seo->absolute($settings['default_image'])), 'Imagen de Configuración SEO que se usa al compartir una página sin imagen propia.'),
            $this->check('same_as', 'Perfiles oficiales de la organización', 5, ! empty($settings['same_as']), 'Agregue la URL de LinkedIn u otros perfiles oficiales en Configuración SEO.'),
            $this->check('analytics', 'Medición de visitas (GA4 o Tag Manager)', 15, filled($site->google_analytics_id) || filled($site->google_tag_manager_id), 'Agregue el ID de Google Analytics 4 o Tag Manager en Configuración › Configuración del sitio.'),
            $this->check('organization', 'Datos de contacto de la organización', 10, filled($contact['phone'] ?? null) && filled($contact['office_text'] ?? null), 'Complete teléfono y dirección en la página Contacto: se publican en los datos estructurados.'),
        ];
    }

    private function imageExists(string $url): bool
    {
        $origin = $this->seo->origin();
        if (! str_starts_with($url, $origin.'/')) {
            return $url !== '';
        }

        $path = rawurldecode((string) parse_url($url, PHP_URL_PATH));

        return is_file(public_path(ltrim($path, '/')))
            || (str_starts_with($path, '/storage/') && is_file(storage_path('app/public/'.substr($path, 9))));
    }

    /** @return array<string, mixed> */
    private function check(string $key, string $label, int $weight, bool $passed, string $help): array
    {
        return ['key' => $key, 'label' => $label, 'weight' => $weight, 'earned' => $passed ? $weight : 0, 'passed' => $passed, 'help' => $help];
    }
}
