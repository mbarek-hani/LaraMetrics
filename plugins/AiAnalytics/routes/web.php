<?php

use Illuminate\Support\Facades\Route;
use Plugins\AiAnalytics\Http\Controllers\AiReportController;

// /plugins/ai-analytics/rapport
Route::get('/rapport', [AiReportController::class, 'rapport'])->name('rapport');
