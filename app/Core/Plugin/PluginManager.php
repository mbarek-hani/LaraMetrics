<?php

namespace App\Core\Plugin;

use App\Models\Plugin as PluginModele;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PluginManager
{
    /** @var array<string, PluginInterface> */
    private array $pluginsDecouverts = [];

    /** @var array<string, PluginInterface> */
    private array $pluginsActifs = [];

    /** @var array<string, PluginInterface[]> */
    private array $hooks = [];

    public function __construct(private PluginDiscovery $discovery) {}

    public function initialiser(): void
    {
        if (!$this->tableExiste()) {
            return;
        }

        $this->pluginsDecouverts = $this->discovery->decouvrir();
        $actifs = $this->getActifsEnBdd();

        foreach ($this->pluginsDecouverts as $id => $plugin) {
            if (in_array($id, $actifs)) {
                $this->chargerPlugin($plugin);
            }
        }
    }

    private function tableExiste(): bool
    {
        try {
            return Schema::hasTable("plugins");
        } catch (\Throwable) {
            return false;
        }
    }

    /** @return string[] */
    private function getActifsEnBdd(): array
    {
        try {
            return Cache::remember(
                "plugins_actifs",
                300,
                fn() => PluginModele::where("actif", true)
                    ->pluck("identifiant")
                    ->toArray(),
            );
        } catch (\Throwable) {
            return [];
        }
    }

    private function chargerPlugin(PluginInterface $plugin): void
    {
        try {
            $plugin->enregistrer();

            foreach ($plugin->getHooks() as $hook) {
                $this->hooks[$hook][] = $plugin;
            }

            $this->pluginsActifs[$plugin->getIdentifiant()] = $plugin;
        } catch (\Throwable $e) {
            Log::error(
                "Plugin [{$plugin->getIdentifiant()}] : " . $e->getMessage(),
            );
        }
    }

    public function activer(string $id): bool
    {
        $plugin = $this->pluginsDecouverts[$id] ?? null;
        if (!$plugin) {
            return false;
        }

        try {
            $plugin->activer();

            PluginModele::updateOrCreate(
                ["identifiant" => $id],
                [
                    "nom" => $plugin->getNom(),
                    "version" => $plugin->getVersion(),
                    "actif" => true,
                    "installe" => true,
                    "active_le" => now(),
                    "installe_le" => now(),
                    "metadonnees" => $plugin->getManifest(),
                ],
            );

            Cache::forget("plugins_actifs");
            $this->chargerPlugin($plugin);
            return true;
        } catch (\Throwable $e) {
            Log::error("Activation plugin [{$id}] : " . $e->getMessage());
            return false;
        }
    }

    public function desactiver(string $id): bool
    {
        $plugin = $this->pluginsActifs[$id] ?? null;
        if (!$plugin) {
            return false;
        }

        try {
            $plugin->desactiver();
            PluginModele::where("identifiant", $id)->update(["actif" => false]);
            unset($this->pluginsActifs[$id]);
            Cache::forget("plugins_actifs");
            return true;
        } catch (\Throwable $e) {
            Log::error("Désactivation plugin [{$id}] : " . $e->getMessage());
            return false;
        }
    }

    /** @return string[] */
    public function executerHook(string $hook, array $donnees = []): array
    {
        $resultats = [];

        foreach ($this->hooks[$hook] ?? [] as $plugin) {
            if (method_exists($plugin, "rendrePourHook")) {
                $resultats[] = $plugin->rendrePourHook($hook, $donnees);
            }
        }

        return $resultats;
    }

    /**
     * Retourne tous les onglets déclarés par les plugins actifs.
     *
     * @return array<int, array{id: string, label: string, icone: string, plugin: string}>
     */
    public function getOnglets(): array
    {
        $onglets = [];

        foreach ($this->pluginsActifs as $plugin) {
            foreach ($plugin->getOnglets() as $onglet) {
                $onglet["plugin"] = $plugin->getIdentifiant();
                $onglets[] = $onglet;
            }
        }

        return $onglets;
    }

    /**
     * Retourne les réglages de tous les plugins actifs.
     *
     * @return array<string, array>
     */
    public function getTousLesReglages(): array
    {
        $reglages = [];

        foreach ($this->pluginsActifs as $plugin) {
            $champs = $plugin->getReglages();
            if (!empty($champs)) {
                $reglages[$plugin->getIdentifiant()] = [
                    "nom" => $plugin->getNom(),
                    "champs" => $champs,
                ];
            }
        }

        return $reglages;
    }

    /** @return array<string, PluginInterface> */
    public function getPluginsDecouverts(): array
    {
        return $this->pluginsDecouverts;
    }

    /** @return array<string, PluginInterface> */
    public function getPluginsActifs(): array
    {
        return $this->pluginsActifs;
    }

    public function estActif(string $id): bool
    {
        return isset($this->pluginsActifs[$id]);
    }
}
