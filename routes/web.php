<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// INICIO
Route::get('/', [SiteController::class, 'home'])->name('home');

// PLATAFORMA
Route::get('/plataforma', [SiteController::class, 'platform'])->name('platform');
