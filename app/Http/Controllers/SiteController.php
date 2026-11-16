<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        return $this->renderPage('home');
    }

    public function platform(): View
    {
        return $this->renderPage('platform');
    }

    private function renderPage(string $page): View
    {
        $sections = PageSection::query()
            ->where('page', $page)
            ->get()
            ->mapWithKeys(fn (PageSection $item): array => [
                $item->section => $item->content ?? [],
            ])
            ->all();

        return view('site', [
            'pageName' => $page,
            'pageContent' => $sections,
            'seo' => $sections['seo'] ?? [],
        ]);
    }
}
