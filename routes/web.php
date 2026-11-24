<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmailPreviewController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('seo.llms');

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/plataforma', [SiteController::class, 'platform'])->name('platform');
Route::get('/financiamiento-estructurado', [SiteController::class, 'financing'])->name('financing');
Route::get('/inversionistas', [SiteController::class, 'investors'])->name('investors');
Route::get('/estrategias-de-inversion', [SiteController::class, 'strategies'])->name('strategies');
Route::get('/insights', [SiteController::class, 'insights'])->name('insights');
Route::get('/insights/{slug}', [SiteController::class, 'insight'])->name('insights.show');
Route::get('/contacto', [SiteController::class, 'contact'])->name('contact');
Route::post('/contacto', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
Route::get('/admin/correos/vista-previa/{mail}', EmailPreviewController::class)
    ->middleware('auth')
    ->name('emails.preview');
