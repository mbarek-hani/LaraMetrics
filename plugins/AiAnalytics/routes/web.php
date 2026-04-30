<?php

use Illuminate\Support\Facades\Route;
use Plugins\AiAnalytics\Http\Controllers\AiReportController;

// POST /plugins/ai-analytics/generer
Route::post('/generer', [AiReportController::class, 'generer'])->name(
    'generer',
);

// GET /plugins/ai-analytics/dernier?site_id=1
Route::get('/dernier', [AiReportController::class, 'dernier'])->name('dernier');

// GET /plugins/ai-analytics/historique?site_id=1
Route::get('/historique', [AiReportController::class, 'historique'])->name(
    'historique',
);

Route::get('/rapport/{id}', [AiReportController::class, 'show'])->name('show');

Route::prefix('/historique')
    ->name('historique.')
    ->group(function () {
        Route::get('/', [AiReportController::class, 'index'])->name('index');
        Route::get('/rapport/{rapport}', [
            AiReportController::class,
            'show',
        ])->name('show');
        Route::delete('/rapport/{rapport}', [
            AiReportController::class,
            'destroy',
        ])->name('destroy');
    });
