<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Services\SiteSeo;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function __construct(private SiteSeo $seo) {}

    public function home(): View
    {
        return $this->renderPage('home');
    }

    public function platform(): View
    {
        return $this->renderPage('platform');
    }

    public function financing(): View
    {
        return $this->renderPage('financing');
    }

    public function investors(): View
    {
        return $this->renderPage('investors');
    }

    public function strategies(): View
    {
        return $this->renderPage('strategies');
    }

    public function contact(): View
    {
        return $this->renderPage('contact');
    }

    public function insights(): View
    {
        $articles = Insight::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        // El destacado más reciente va en grande; si no hay, el artículo más reciente.
        $featured = $articles->firstWhere('is_featured', true) ?? $articles->first();

        return $this->renderPage('insights', [
            'featured' => $featured?->toCard(),
            'articles' => $articles
                ->reject(fn (Insight $article): bool => $featured && $article->is($featured))
                ->map(fn (Insight $article): array => $article->toCard())
                ->values()
                ->all(),
        ]);
    }

    public function insight(string $slug): View
    {
        $article = Insight::query()->published()->where('slug', $slug)->firstOrFail();

        $related = Insight::query()
            ->published()
            ->whereKeyNot($article->getKey())
            ->orderByRaw('category = ? desc', [$article->category])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Insight $item): array => $item->toCard())
            ->all();

        $sections = $this->sections('insights');

        return $this->view('insight', [
            'article' => [
                ...$article->toCard(),
                'body' => RichContentRenderer::make($article->body)
                    ->fileAttachmentsDisk('public')
                    ->toHtml(),
            ],
            'related' => $related,
            'listing' => $sections['listing'] ?? [],
            'subscribe' => $sections['subscribe'] ?? [],
        ], $this->seo->metadata('insights', [], $article));
    }

    private function renderPage(string $page, array $extra = []): View
    {
        $sections = $this->sections($page);

        return $this->view($page, [...$sections, ...$extra], $this->seo->metadata($page, $sections));
    }

    /** @param array<string, mixed> $meta */
    private function view(string $pageName, array $pageContent, array $meta): View
    {
        // El SEO de cada página se publica en <head>; Vue no lo necesita.
        unset($pageContent['seo']);

        return view('site', [
            'pageName' => $pageName,
            'pageContent' => $pageContent,
            'sharedContent' => $this->sections('global'),
            'siteSettings' => SiteSetting::current(),
            'meta' => $meta,
        ]);
    }

    /** @return array<string, array<string, mixed>> */
    private function sections(string $page): array
    {
        return PageSection::query()
            ->where('page', $page)
            ->get()
            ->mapWithKeys(fn (PageSection $item): array => [
                $item->section => $item->content ?? [],
            ])
            ->all();
    }
}
