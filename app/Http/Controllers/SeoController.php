<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Models\PageSection;
use App\Services\SiteSeo;
use Symfony\Component\HttpFoundation\Response;
use XMLWriter;

class SeoController extends Controller
{
    public function robots(SiteSeo $seo): Response
    {
        if (! $seo->publicIndexingEnabled()) {
            return response("User-agent: *\nDisallow: /\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $rules = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /livewire\nDisallow: /up\n\n";

        return response($rules.'Sitemap: '.$seo->absolute('/sitemap.xml')."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(SiteSeo $seo): Response
    {
        $writer = new XMLWriter;
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElement('urlset');
        $writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($seo->sitemapEntries() as $entry) {
            $writer->startElement('url');
            $writer->writeElement('loc', $seo->absolute($entry['path']));
            if ($entry['lastmod']) {
                $writer->writeElement('lastmod', $entry['lastmod']);
            }
            $writer->endElement();
        }

        $writer->endElement();
        $writer->endDocument();

        return response($writer->outputMemory(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** Resumen del sitio para asistentes de IA (formato llms.txt). */
    public function llms(SiteSeo $seo): Response
    {
        abort_unless((bool) $seo->settings()['llms_enabled'], 404);

        $lines = ['# GDI Valores', '', '> Plataforma institucional regulada para la administración de capital y la estructuración de financiamiento en el mercado de valores de Guatemala.', '', '## Páginas', ''];

        foreach (SiteSeo::PAGES as $page => $info) {
            $sections = PageSection::query()->where('page', $page)->pluck('content', 'section')->all();
            $meta = $seo->metadata($page, $sections);
            $lines[] = '- ['.$info['name'].']('.$meta['canonical'].'): '.$meta['description'];
        }

        $articles = Insight::query()->published()->where('robots_index', true)->orderByDesc('published_at')->limit(50)->get();
        if ($articles->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '## Insights';
            $lines[] = '';
            foreach ($articles as $article) {
                $lines[] = '- ['.$article->title.']('.$seo->absolute(route('insights.show', $article->slug, false)).'): '.$seo->plain((string) $article->excerpt, 200);
            }
        }

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
