<?php

namespace App\Http\Middleware;

use App\Services\SiteSeo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Pide no indexar el panel, los errores y todo el sitio fuera de producción. */
class SeoHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->is('admin', 'admin/*', 'livewire*', 'up') || $response->getStatusCode() >= 400) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        } elseif (! app(SiteSeo::class)->publicIndexingEnabled()) {
            $response->headers->set('X-Robots-Tag', 'noindex, follow');
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
