<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\TrackerJsController;
use Illuminate\Support\Facades\Route;

Route::get("/", fn() => redirect()->route("dashboard"));
Route::get("/tracker.js", \App\Http\Controllers\TrackerJsController::class);

Route::middleware(["auth"])->group(function () {
    Route::get("/dashboard", [DashboardController::class, "index"])->name(
        "dashboard",
    );
    Route::get("/dashboard/stats", [DashboardController::class, "stats"])->name(
        "dashboard.stats",
    );
    Route::post("/dashboard/reglages", [
        DashboardController::class,
        "sauvegarderReglages",
    ])->name("dashboard.reglages");

    Route::resource("sites", SiteController::class);

    Route::get("/plugins", [PluginController::class, "index"])->name(
        "plugins.index",
    );
    Route::post("/plugins/{identifiant}/activer", [
        PluginController::class,
        "activer",
    ])->name("plugins.activer");
    Route::post("/plugins/{identifiant}/desactiver", [
        PluginController::class,
        "desactiver",
    ])->name("plugins.desactiver");
});

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );
});

require __DIR__ . "/auth.php";
