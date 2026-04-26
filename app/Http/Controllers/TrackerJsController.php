<?php

namespace App\Http\Controllers;

use App\Core\Plugin\PluginManager;
use Illuminate\Http\Response;

class TrackerJsController extends Controller
{
    public function __invoke(): Response
    {
        $coreJs = file_get_contents(resource_path("js/tracker-core.js"));

        // Injecter le JS des plugins
        $manager = app(PluginManager::class);
        $pluginJs = $manager->getTrackerJavaScript();

        $js = str_replace("/* PLUGIN_INJECTION */", $pluginJs, $coreJs);

        return response($js, 200)
            ->header("Content-Type", "application/javascript")
            ->header("Cache-Control", "public, max-age=3600");
    }
}
