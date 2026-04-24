<?php

namespace App\Core\Plugin;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * Classe de base pour tous les plugins.
 * Fournit des comportements par défaut réutilisables.
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * Chemin absolu vers le dossier racine du plugin.
     */
    protected string $cheminRacine;

    /**
     * Données du manifest.json du plugin.
     *
     * @var array<string, mixed>
     */
    protected array $manifest = [];

    public function __construct()
    {
        // On remonte de /src jusqu'à la racine du plugin
        $refClass = new \ReflectionClass(static::class);
        $this->cheminRacine = dirname($refClass->getFileName(), 2);

        $this->chargerManifest();
    }

    /**
     * Charge et parse le fichier manifest.json du plugin.
     */
    protected function chargerManifest(): void
    {
        $cheminManifest = $this->cheminRacine.'/manifest.json';

        if (! file_exists($cheminManifest)) {
            Log::warning(
                "Plugin [{$this->getIdentifiant()}] : manifest.json introuvable.",
            );

            return;
        }

        $contenu = file_get_contents($cheminManifest);
        $this->manifest = json_decode($contenu, true) ?? [];
    }

    /**
     * Implémentation par défaut : enregistre les routes si elles existent.
     */
    public function enregistrer(): void
    {
        $this->enregistrerRoutes();
        $this->enregistrerVues();
    }

    /**
     * Charge le fichier routes/web.php du plugin s'il existe.
     */
    protected function enregistrerRoutes(): void
    {
        $fichierRoutes = $this->cheminRacine.'/routes/web.php';

        if (file_exists($fichierRoutes)) {
            Route::middleware(['web', 'auth'])
                ->prefix('plugins/'.$this->getIdentifiant())
                ->name('plugin.'.$this->getIdentifiant().'.')
                ->group($fichierRoutes);
        }
    }

    /**
     * Enregistre le namespace des vues du plugin.
     * Utilisation dans Blade : @include('ai-analytics::widget')
     */
    protected function enregistrerVues(): void
    {
        $dossierVues = $this->cheminRacine.'/resources/views';

        if (is_dir($dossierVues)) {
            app('view')->addNamespace($this->getIdentifiant(), $dossierVues);
        }
    }

    /**
     * Lance les migrations du plugin.
     */
    public function activer(): void
    {
        $dossierMigrations = $this->cheminRacine.'/database/migrations';

        if (is_dir($dossierMigrations)) {
            Artisan::call('migrate', [
                '--path' => $this->getCheminRelatif($dossierMigrations),
                '--force' => true,
            ]);

            Log::info(
                "Plugin [{$this->getIdentifiant()}] : migrations exécutées.",
            );
        }
    }

    /**
     * Implémentation par défaut de désactivation (rien à faire).
     */
    public function desactiver(): void
    {
        Log::info("Plugin [{$this->getIdentifiant()}] : désactivé.");
    }

    /**
     * Implémentation par défaut de désinstallation.
     * À surcharger dans chaque plugin pour rollback des migrations.
     */
    public function desinstaller(): void
    {
        Log::info("Plugin [{$this->getIdentifiant()}] : désinstallé.");
    }

    /**
     * Par défaut, aucun hook déclaré.
     *
     * @return string[]
     */
    public function getHooks(): array
    {
        return [];
    }

    /**
     * Retourne le chemin relatif depuis la racine Laravel.
     * Nécessaire pour la commande artisan migrate --path.
     */
    protected function getCheminRelatif(string $cheminAbsolu): string
    {
        return str_replace(base_path().'/', '', $cheminAbsolu);
    }

    /**
     * Accesseur pour les données du manifest.
     *
     * @return array<string, mixed>
     */
    public function getManifest(): array
    {
        return $this->manifest;
    }
}
