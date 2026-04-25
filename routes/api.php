<?php

use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::post('/track', [TrackingController::class, 'track'])->name(
    'tracking.track',
);

Route::options('/track', function () {
    return response()
        ->noContent(204)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        ->header(
            'Access-Control-Allow-Headers',
            'Content-Type, Accept, X-Requested-With',
        );
});
