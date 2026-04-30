<?php

namespace App\Http\Controllers;

use App\Core\Plugin\PluginManager;
use App\Models\Plugin as PluginModele;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $manager = app(PluginManager::class);
        $reglages = $manager->getTousLesReglages();

        // Charger les valeurs actuelles pour chaque plugin
        $valeurs = [];
        foreach ($reglages as $pluginId => $plugin) {
            $config =
                PluginModele::where('identifiant', $pluginId)->value(
                    'configuration',
                ) ?? [];
            $valeurs[$pluginId] = $config;
        }

        return view('settings.index', compact('reglages', 'valeurs'));
    }

    public function sauvegarderReglages(Request $request): JsonResponse
    {
        $request->validate([
            'plugin' => ['required', 'string'],
            'reglages' => ['required', 'array'],
        ]);

        $plugin = PluginModele::where('identifiant', $request->plugin)->first();

        if (! $plugin) {
            return response()->json(['erreur' => 'Plugin introuvable'], 404);
        }

        $config = $plugin->configuration ?? [];
        $config = array_merge($config, $request->reglages);
        $plugin->update(['configuration' => $config]);

        return response()->json([
            'succes' => true,
            'message' => 'Réglages sauvegardés.',
        ]);
    }
}
