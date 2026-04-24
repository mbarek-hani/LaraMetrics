<?php
use Plugins\AiAnalytics\Http\Controllers\AiReportController;
use Illuminate\Support\Facades\Route;

// /plugins/ai-analytics/rapport
Route::get("/rapport", [AiReportController::class, "rapport"])->name("rapport");
