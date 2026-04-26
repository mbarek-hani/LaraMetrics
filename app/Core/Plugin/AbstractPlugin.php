<?php

namespace App\Core\Plugin;

use App\Models\Plugin;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

abstract class AbstractPlugin implements PluginInterface
{
    protected string $cheminRacine;

    /** @var array<string, mixed> */
    protected array $manifest = [];

    public function __construct()
    {
        $refClass = new \ReflectionClass(static::class);
        $this->cheminRacine = dirname($refClass->getFileName(), 2);
        $this->chargerManifest();
    }

    protected function chargerManifest(): void
    {
        $chemin = $this->cheminRacine.'/manifest.json';

        if (file_exists($chemin)) {
            $this->manifest =
                json_decode(file_get_contents($chemin), true) ?? [];
        }
    }

    // Cycle de vie

    public function enregistrer(): void
    {
        $this->enregistrerRoutes();
        $this->enregistrerVues();
        $this->publierAssets();
    }

    protected function enregistrerRoutes(): void
    {
        $fichier = $this->cheminRacine.'/routes/web.php';

        if (file_exists($fichier)) {
            Route::middleware(['web', 'auth'])
                ->prefix('plugins/'.$this->getIdentifiant())
                ->name('plugin.'.$this->getIdentifiant().'.')
                ->group($fichier);
        }
    }

    protected function enregistrerVues(): void
    {
        $dossier = $this->cheminRacine.'/resources/views';

        if (is_dir($dossier)) {
            app('view')->addNamespace($this->getIdentifiant(), $dossier);
        }
    }

    /**
     * Copie les assets statiques (CSS, JS) vers public/plugins/{id}/
     */
    protected function publierAssets(): void
    {
        $source = $this->cheminRacine.'/resources/assets';
        $dest = public_path('plugins/'.$this->getIdentifiant());

        if (! is_dir($source)) {
            return;
        }

        if (! is_dir($dest)) {
            mkdir($dest, 0755, true);
        }

        $fichiers = glob($source.'/*');
        foreach ($fichiers as $fichier) {
            $nomFichier = basename($fichier);
            $destFichier = $dest.'/'.$nomFichier;

            if (
                ! file_exists($destFichier) ||
                filemtime($fichier) > filemtime($destFichier)
            ) {
                copy($fichier, $destFichier);
            }
        }
    }

    public function activer(): void
    {
        $dossier = $this->cheminRacine.'/database/migrations';

        if (is_dir($dossier)) {
            Artisan::call('migrate', [
                '--path' => str_replace(base_path().'/', '', $dossier),
                '--force' => true,
            ]);
        }

        $this->publierAssets();
    }

    public function desactiver(): void
    {
        Log::info("Plugin [{$this->getIdentifiant()}] désactivé.");
    }

    public function desinstaller(): void
    {
        // Supprimer les assets publiés
        $dest = public_path('plugins/'.$this->getIdentifiant());
        if (is_dir($dest)) {
            array_map('unlink', glob($dest.'/*'));
            rmdir($dest);
        }

        Log::info("Plugin [{$this->getIdentifiant()}] désinstallé.");
    }

    // ─── Implémentations par défaut (ne rien faire) ─────────────

    public function getHooks(): array
    {
        return [];
    }

    public function getOnglets(): array
    {
        return [];
    }

    public function getReglages(): array
    {
        return [];
    }

    public function getNavigationItems(): array
    {
        return [];
    }

    public function enrichirTracking(array $donnees, $request): ?array
    {
        return null;
    }

    public function apresVisite($visite, array $donnees, $request): void {}

    public function apresEvenement(
        $evenement,
        array $donnees,
        $request,
    ): void {}

    public function getTrackerJavaScript(): ?string
    {
        return null;
    }

    public function getCssPath(): ?string
    {
        $chemin = 'plugins/'.$this->getIdentifiant().'/style.css';

        if (file_exists(public_path($chemin))) {
            return $chemin;
        }

        return null;
    }

    public function getManifest(): array
    {
        return $this->manifest;
    }

    // Helpers pour les plugins

    /**
     * Récupère une valeur de configuration du plugin depuis la BDD.
     */
    protected function config(string $cle, mixed $defaut = null): mixed
    {
        $plugin = Plugin::where(
            'identifiant',
            $this->getIdentifiant(),
        )->first();

        if (! $plugin || ! $plugin->configuration) {
            return $defaut;
        }

        return $plugin->configuration[$cle] ?? $defaut;
    }

    /**
     * Chemin absolu vers la racine du plugin.
     */
    protected function chemin(string $relatif = ''): string
    {
        return $this->cheminRacine.
            ($relatif ? '/'.ltrim($relatif, '/') : '');
    }
}
