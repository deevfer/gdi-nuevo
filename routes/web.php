<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/plataforma', [SiteController::class, 'platform'])->name('platform');
Route::get('/financiamiento-estructurado', [SiteController::class, 'financing'])->name('financing');
