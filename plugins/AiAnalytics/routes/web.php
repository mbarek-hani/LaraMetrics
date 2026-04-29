<?php

use Plugins\AiAnalytics\Http\Controllers\AiReportController;
use Illuminate\Support\Facades\Route;

// POST /plugins/ai-analytics/generer
Route::post('/generer', [AiReportController::class, 'generer'])
    ->name('generer');

// GET /plugins/ai-analytics/dernier?site_id=1
Route::get('/dernier', [AiReportController::class, 'dernier'])
    ->name('dernier');

// GET /plugins/ai-analytics/historique?site_id=1
Route::get('/historique', [AiReportController::class, 'historique'])
    ->name('historique');

Route::get('/rapport/{id}', [AiReportController::class, 'show'])->name('show');
