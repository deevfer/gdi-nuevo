<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        $sections = PageSection::query()
            ->where('page', 'home')
            ->get()
            ->mapWithKeys(fn (PageSection $item) => [
                $item->section => $item->content ?? [],
            ])
            ->all();

        return view('site', [
            'homeContent' => $sections,
            'seo' => $sections['seo'] ?? [],
        ]);
    }
}
