```markdown
# LaraMetrics — Guide de Développement de Plugins

> **Version** : 1.0.0
> **Application** : LaraMetrics
> **Stack** : Laravel 11 · Alpine.js · Tailwind CSS · Heroicons

---

## Table des matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Comment fonctionne le système](#2-comment-fonctionne-le-système)
3. [Démarrage rapide](#3-démarrage-rapide)
4. [Structure des fichiers](#4-structure-des-fichiers)
5. [Le fichier manifest.json](#5-le-fichier-manifestjson)
6. [La classe principale](#6-la-classe-principale)
7. [Ce qu'un plugin peut faire](#7-ce-quun-plugin-peut-faire)
   - [7.1 Onglets dans le dashboard](#71-onglets-dans-le-dashboard)
   - [7.2 Pages dédiées et navigation](#72-pages-dédiées-et-navigation)
   - [7.3 Réglages](#73-réglages)
   - [7.4 Hooks visuels](#74-hooks-visuels)
   - [7.5 Enrichir le tracking](#75-enrichir-le-tracking)
   - [7.6 Réagir aux visites et événements](#76-réagir-aux-visites-et-événements)
   - [7.7 Étendre le script tracker.js](#77-étendre-le-script-trackerjs)
   - [7.8 Créer ses propres tables](#78-créer-ses-propres-tables)
   - [7.9 Stocker des métadonnées de visite](#79-stocker-des-métadonnées-de-visite)
   - [7.10 Assets et CSS personnalisés](#710-assets-et-css-personnalisés)
8. [Composants UI disponibles](#8-composants-ui-disponibles)
9. [Cycle de vie d'un plugin](#9-cycle-de-vie-dun-plugin)
10. [Conventions et bonnes pratiques](#10-conventions-et-bonnes-pratiques)
11. [Exemple complet : plugin ScorePage](#11-exemple-complet--plugin-scorepage)
12. [Référence API complète](#12-référence-api-complète)
13. [Dépannage](#13-dépannage)

---

## 1. Vue d'ensemble

LaraMetrics repose sur une architecture modulaire. Le **core** gère le
tracking, l'authentification et l'interface principale. Tout le reste
peut être implémenté sous forme de **plugin**.

### Qu'est-ce qu'un plugin ?

Un plugin est un dossier autonome placé dans `/plugins` à la racine du
projet. Il est **auto-découvert** au démarrage de l'application : aucune
modification du code source du core n'est nécessaire pour l'installer.

### Ce qu'un plugin peut faire

| Capacité | Description |
|---|---|
| **Onglets dashboard** | Ajouter des onglets dans le tableau de bord principal |
| **Pages dédiées** | Créer des pages complètes avec leurs routes |
| **Navigation** | Ajouter des liens dans la barre de navigation |
| **Réglages** | Exposer des options de configuration à l'utilisateur |
| **Hooks visuels** | Injecter du HTML à des points précis de l'interface |
| **Tracking enrichi** | Modifier les données avant qu'elles soient enregistrées |
| **Post-tracking** | Réagir après l'enregistrement d'une visite ou d'un événement |
| **Tracker JS** | Injecter du JavaScript dans le script de tracking client |
| **Base de données** | Créer ses propres tables via des migrations |
| **Métadonnées** | Attacher des données personnalisées aux visites |
| **Assets** | Charger un fichier CSS précompilé spécifique au plugin |

---

## 2. Comment fonctionne le système

### Démarrage de l'application

```
Application Laravel démarre
         │
         ▼
PluginServiceProvider::boot()
         │
         ├─► PluginAutoloader::register()
         │       Enregistre un autoloader PSR-4 dynamique
         │       qui mappe Plugins\{Nom}\ → plugins/{Nom}/src/
         │
         └─► PluginManager::initialiser()
                 │
                 ├─► PluginDiscovery::decouvrir()
                 │       Scanne plugins/*/manifest.json
                 │       Instancie chaque classe de plugin valide
                 │
                 └─► Pour chaque plugin actif en base de données :
                         plugin->enregistrer()
                         → Routes, vues, assets enregistrés
                         → Hooks collectés
                         → Liens de navigation collectés
```

### Requête HTTP entrante

```
Requête HTTP
     │
     ▼
Navigation → PluginManager::getNavigationItems()
             Injecte les liens des plugins actifs

     │
     ▼
Dashboard  → Directive Blade @hook('tab.mon-onglet')
             PluginManager::executerHook()
             → plugin->rendrePourHook() pour chaque plugin

     │
     ▼
POST /api/track (tracker.js)
     │
     ├─► PluginManager::enrichirTracking()
     │       Chaque plugin peut modifier les données
     │
     ├─► TrackingService::enregistrerVisite()
     │       Visite sauvegardée en base
     │
     └─► PluginManager::apresVisite()
             Chaque plugin peut réagir
```

### Autoloader dynamique

Contrairement à une installation Composer classique, les plugins sont
chargés par un autoloader PHP personnalisé enregistré avec
`spl_autoload_register`. Cela signifie :

- **Pas de** `composer dump-autoload` après l'ajout d'un plugin
- **Pas de** modification de `composer.json`
- Le nom du dossier **doit** être en PascalCase (exigence PSR-4)

```
Namespace demandé : Plugins\AiAnalytics\Http\Controllers\AiReportController
                              │
                              ▼
Autoloader mappe vers : plugins/AiAnalytics/src/Http/Controllers/AiReportController.php
```

---

## 3. Démarrage rapide

Créez un plugin fonctionnel en 4 étapes.

### Étape 1 — Créer les dossiers

```bash
mkdir -p plugins/MonPlugin/src
mkdir -p plugins/MonPlugin/resources/views
mkdir -p plugins/MonPlugin/routes
```

### Étape 2 — Créer le manifest

```bash
touch plugins/MonPlugin/manifest.json
```

```json
{
    "identifiant": "mon-plugin",
    "nom": "Mon Plugin",
    "version": "1.0.0",
    "description": "Description courte de ce que fait le plugin.",
    "auteur": "Votre Nom",
    "classe": "Plugins\\MonPlugin\\MonPlugin"
}
```

### Étape 3 — Créer la classe principale

```bash
touch plugins/MonPlugin/src/MonPlugin.php
```

```php
<?php

namespace Plugins\MonPlugin;

use App\Core\Plugin\AbstractPlugin;

class MonPlugin extends AbstractPlugin
{
    public function getIdentifiant(): string
    {
        return 'mon-plugin';
    }

    public function getNom(): string
    {
        return 'Mon Plugin';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
```

### Étape 4 — Activer le plugin

Rendez-vous dans l'interface d'administration à la page **Plugins**
et cliquez sur **Activer** en face de votre plugin.

Le plugin est maintenant découvert, ses migrations sont exécutées
et ses routes sont enregistrées.

---

## 4. Structure des fichiers

```
plugins/
└── MonPlugin/                              ← PascalCase obligatoire
    │
    ├── manifest.json                       ← Obligatoire
    │
    ├── src/                                ← Tout le code PHP
    │   ├── MonPlugin.php                   ← Classe principale (obligatoire)
    │   │
    │   ├── Http/
    │   │   └── Controllers/
    │   │       └── MonController.php
    │   │
    │   ├── Models/
    │   │   └── MonModele.php
    │   │
    │   └── Services/
    │       └── MonService.php
    │
    ├── database/
    │   └── migrations/
    │       └── 2024_01_01_000001_create_mon_plugin_table.php
    │
    ├── resources/
    │   ├── views/                          ← Vues Blade
    │   │   ├── tab.blade.php
    │   │   ├── page.blade.php
    │   │   └── widget.blade.php
    │   │
    │   └── assets/                         ← Fichiers statiques
    │       └── style.css                   ← CSS précompilé (optionnel)
    │
    └── routes/
        └── web.php                         ← Routes du plugin
```

### Règles de nommage des dossiers

| Élément | Convention | Exemple |
|---|---|---|
| Dossier racine du plugin | PascalCase | `MonPlugin/` |
| Namespace PHP | `Plugins\{PascalCase}` | `Plugins\MonPlugin` |
| Identifiant (manifest) | kebab-case | `mon-plugin` |
| Tables de base de données | snake_case avec préfixe | `mon_plugin_data` |
| Noms de routes | dot notation | `plugin.mon-plugin.index` |
| Namespace des vues | kebab-case | `mon-plugin::tab` |

---

## 5. Le fichier manifest.json

Fichier **obligatoire** à la racine de chaque plugin. Il est lu lors
de la découverte et ses données sont stockées en base de données lors
de l'activation.

### Exemple complet

```json
{
    "identifiant": "mon-plugin",
    "nom": "Mon Plugin",
    "version": "1.2.0",
    "description": "Ce plugin fait ceci et cela de manière optimale.",
    "auteur": "Prénom Nom",
    "email": "auteur@exemple.fr",
    "url": "https://exemple.fr/plugins/mon-plugin",
    "licence": "MIT",
    "classe": "Plugins\\MonPlugin\\MonPlugin",
    "version_app_min": "1.0.0",
    "dependances": [],
    "hooks": [
        "dashboard.widgets",
        "tab.mon-onglet"
    ]
}
```

### Description des champs

#### Champs obligatoires

| Champ | Type | Description |
|---|---|---|
| `identifiant` | `string` | Identifiant unique en kebab-case. Doit être unique parmi tous les plugins. |
| `nom` | `string` | Nom lisible affiché dans l'interface d'administration. |
| `version` | `string` | Version au format semver : `MAJEUR.MINEUR.PATCH` |
| `classe` | `string` | Nom de classe pleinement qualifié (FQCN) de la classe principale. |

#### Champs optionnels

| Champ | Type | Description |
|---|---|---|
| `description` | `string` | Description courte affichée dans la liste des plugins. |
| `auteur` | `string` | Nom de l'auteur ou de l'organisation. |
| `email` | `string` | Email de contact pour le support. |
| `url` | `string` | URL de la page du plugin (documentation, dépôt, etc.). |
| `licence` | `string` | Identifiant de licence SPDX (ex : `MIT`, `GPL-3.0`). |
| `version_app_min` | `string` | Version minimale de LaraMetrics requise. |
| `dependances` | `array` | Liste des identifiants de plugins dont ce plugin dépend. |
| `hooks` | `array` | Liste indicative des hooks utilisés (documentation uniquement). |

---

## 6. La classe principale

### Structure de base

```php
<?php

namespace Plugins\MonPlugin;

use App\Core\Plugin\AbstractPlugin;

class MonPlugin extends AbstractPlugin
{
    // ─── Les trois méthodes suivantes sont OBLIGATOIRES ─────────

    public function getIdentifiant(): string
    {
        return 'mon-plugin'; // Doit correspondre au champ "identifiant" du manifest
    }

    public function getNom(): string
    {
        return 'Mon Plugin';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    // ─── Toutes les autres méthodes sont OPTIONNELLES ───────────
    // Surchargez uniquement celles dont vous avez besoin.
    // AbstractPlugin fournit des implémentations par défaut
    // qui ne font rien pour chaque méthode optionnelle.
}
```

### Héritage et méthodes disponibles

`AbstractPlugin` implémente `PluginInterface` et fournit :

- L'enregistrement automatique des **routes** (`routes/web.php`)
- L'enregistrement automatique du **namespace de vues** (`resources/views/`)
- La **publication des assets** (`resources/assets/` → `public/plugins/{id}/`)
- L'exécution automatique des **migrations** à l'activation
- Un helper `$this->config($cle)` pour lire la configuration
- Un helper `$this->chemin($relatif)` pour les chemins de fichiers

---

## 7. Ce qu'un plugin peut faire

### 7.1 Onglets dans le dashboard

Les onglets s'affichent dans la barre de navigation du tableau de bord,
à côté de "Vue d'ensemble" et "Événements".

#### Déclarer les onglets

```php
public function getOnglets(): array
{
    return [
        [
            'id'    => 'mon-onglet',  // Identifiant unique de l'onglet
            'label' => 'Mon Analyse', // Texte affiché dans l'onglet
            'icone' => 'chart-bar',   // Nom d'icône Heroicons
        ],
    ];
}
```

#### Connecter l'onglet à un hook

```php
public function getHooks(): array
{
    // Le hook "tab.{id}" est automatiquement lié à l'onglet "{id}"
    return ['tab.mon-onglet'];
}

public function rendrePourHook(string $hook, array $donnees = []): string
{
    return match ($hook) {
        'tab.mon-onglet' => view('mon-plugin::tab', $donnees)->render(),
        default          => '',
    };
}
```

#### Créer la vue de l'onglet

```blade
{{-- plugins/MonPlugin/resources/views/tab.blade.php --}}

<div x-data="monOnglet()" x-init="charger()">

    <x-card titre="Mon Analyse">

        {{-- État : chargement --}}
        <div x-show="chargement" class="flex items-center justify-center py-8">
            <x-icon name="arrow-path" class="w-5 h-5 text-gray-400 animate-spin" />
            <span class="ml-2 text-sm text-gray-500">Chargement...</span>
        </div>

        {{-- État : erreur --}}
        <div x-show="erreur" class="bg-red-50 border border-red-200 rounded p-3">
            <p class="text-sm text-red-700" x-text="erreur"></p>
        </div>

        {{-- État : données --}}
        <div x-show="!chargement && !erreur">
            <p class="text-sm text-gray-600">Contenu de l'onglet.</p>
        </div>

    </x-card>

</div>

<script>
function monOnglet() {
    return {
        chargement : true,
        erreur     : null,
        donnees    : null,

        async charger() {
            try {
                const r = await fetch('/plugins/mon-plugin/donnees', {
                    headers: {
                        'Accept'       : 'application/json',
                        'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                if (!r.ok) throw new Error('Erreur ' + r.status);
                this.donnees = await r.json();
            } catch (e) {
                this.erreur = e.message;
            } finally {
                this.chargement = false;
            }
        },
    }
}
</script>
```

---

### 7.2 Pages dédiées et navigation

#### Déclarer les routes du plugin

```php
// plugins/MonPlugin/routes/web.php

use Plugins\MonPlugin\Http\Controllers\MonController;
use Illuminate\Support\Facades\Route;

// Préfixe automatique : /plugins/mon-plugin/
// Préfixe de nom automatique : plugin.mon-plugin.

Route::get('/', [MonController::class, 'index'])->name('index');
// URL : /plugins/mon-plugin/       Nom : plugin.mon-plugin.index

Route::get('/rapport', [MonController::class, 'rapport'])->name('rapport');
// URL : /plugins/mon-plugin/rapport   Nom : plugin.mon-plugin.rapport

Route::post('/action', [MonController::class, 'action'])->name('action');
// URL : /plugins/mon-plugin/action    Nom : plugin.mon-plugin.action
```

> Toutes les routes du plugin sont automatiquement protégées par le
> middleware `auth`. Il n'est pas nécessaire de l'ajouter manuellement.

#### Créer un controller

```php
<?php

namespace Plugins\MonPlugin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonController extends Controller
{
    /**
     * Page principale du plugin.
     */
    public function index()
    {
        return view('mon-plugin::page', [
            'donnees' => $this->chargerDonnees(),
        ]);
    }

    /**
     * Endpoint JSON pour les requêtes Alpine.js.
     */
    public function donnees(): JsonResponse
    {
        return response()->json([
            'total' => 42,
            'liste' => [],
        ]);
    }
}
```

#### Créer la vue de la page

```blade
{{-- plugins/MonPlugin/resources/views/page.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Mon Plugin</h2>
            <x-button variant="primary" href="{{ route('plugin.mon-plugin.action') }}">
                <x-icon name="plus" class="w-4 h-4" />
                Nouvelle action
            </x-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <x-card titre="Vue d'ensemble">
                <p class="text-sm text-gray-600">Contenu de la page.</p>
            </x-card>

        </div>
    </div>
</x-app-layout>
```

#### Ajouter des liens dans la navigation

```php
public function getNavigationItems(): array
{
    return [
        [
            'label' => 'Mon Plugin',                    // Texte du lien
            'route' => 'plugin.mon-plugin.index',       // Nom de la route
            'icone' => 'chart-bar',                     // Icône Heroicons
        ],
    ];
}
```

Les liens apparaissent automatiquement dans la navigation **desktop** et
**mobile**. L'état actif est géré automatiquement.

---

### 7.3 Réglages

Les réglages permettent à l'utilisateur de configurer le plugin depuis
l'onglet **Réglages** du dashboard. Chaque plugin dispose de sa propre
section de réglages, clairement séparée des autres.

#### Déclarer les champs

```php
public function getReglages(): array
{
    return [
        [
            'cle'         => 'cle_api',        // Clé de stockage en BDD
            'label'       => 'Clé API',         // Label affiché
            'type'        => 'password',        // Type du champ
            'obligatoire' => true,              // Affiche une étoile rouge
            'placeholder' => 'sk-...',          // Placeholder du champ
            'aide'        => 'Obtenir une clé sur api.exemple.fr',
        ],
        [
            'cle'         => 'fournisseur',
            'label'       => 'Fournisseur',
            'type'        => 'select',
            'options'     => ['openai', 'groq', 'mistral'],
            'obligatoire' => true,
            'aide'        => 'Choisissez votre fournisseur IA.',
        ],
        [
            'cle'         => 'notes',
            'label'       => 'Notes',
            'type'        => 'textarea',
            'obligatoire' => false,
            'aide'        => 'Notes internes, non utilisées par le plugin.',
        ],
    ];
}
```

#### Types de champs disponibles

| Type | Description | Champs supplémentaires |
|---|---|---|
| `text` | Champ texte libre | `placeholder` |
| `password` | Champ masqué | `placeholder` |
| `select` | Menu déroulant | `options` (array) |
| `textarea` | Texte multiligne | `placeholder` |

#### Lire la configuration depuis le plugin

```php
// Dans la classe du plugin ou dans un service
$cleApi      = $this->config('cle_api');
$fournisseur = $this->config('fournisseur', 'openai'); // Valeur par défaut

// Dans un Controller ou Service externe
use App\Models\Plugin;

$plugin = Plugin::where('identifiant', 'mon-plugin')->first();
$cleApi = $plugin?->configuration['cle_api'] ?? null;
```

---

### 7.4 Hooks visuels

Les hooks permettent d'injecter du contenu HTML à des emplacements
précis de l'interface sans modifier les vues du core.

#### Hooks disponibles

| Hook | Emplacement dans l'interface |
|---|---|
| `dashboard.widgets` | Bas de l'onglet "Vue d'ensemble" du dashboard |
| `tab.{id}` | Contenu d'un onglet personnalisé (voir section 7.1) |

#### Utilisation

```php
// 1. Déclarer les hooks utilisés
public function getHooks(): array
{
    return ['dashboard.widgets'];
}

// 2. Retourner le contenu HTML pour chaque hook
public function rendrePourHook(string $hook, array $donnees = []): string
{
    return match ($hook) {
        'dashboard.widgets' => view('mon-plugin::widget', $donnees)->render(),
        default             => '',
    };
}
```

> **Note** : Le rendu des hooks se fait côté serveur au moment de la
> génération de la page. Pour du contenu dynamique, utilisez Alpine.js
> avec un appel `fetch()` vers un endpoint de votre plugin.

---

### 7.5 Enrichir le tracking

Cette méthode est appelée pour chaque visite ou événement **avant**
que les données ne soient enregistrées en base. Elle permet de
modifier, filtrer ou enrichir les données de tracking.

```php
/**
 * @param  array   $donnees  Données brutes du tracker.js
 * @param  mixed   $request  Illuminate\Http\Request
 * @return array|null        Données enrichies, ou null pour ne rien modifier
 */
public function enrichirTracking(array $donnees, $request): ?array
{
    // Exemple 1 : normaliser le chemin URL
    if (isset($donnees['chemin'])) {
        $donnees['chemin'] = rtrim(strtolower($donnees['chemin']), '/') ?: '/';
    }

    // Exemple 2 : filtrer certains chemins
    $cheminsIgnores = ['/health', '/ping', '/status'];
    if (in_array($donnees['chemin'] ?? '', $cheminsIgnores)) {
        // Retourner null indique au TrackingService d'ignorer cette visite
        return null;
    }

    // Exemple 3 : ajouter des données calculées
    $donnees['heure_locale'] = now()->format('H');

    return $donnees;
}
```

> **Attention** : Cette méthode est appelée pour **chaque** visite.
> Gardez son exécution rapide. Évitez les appels API ou les requêtes
> en base de données dans cette méthode.

---

### 7.6 Réagir aux visites et événements

Ces méthodes sont appelées **après** l'enregistrement en base de données.
Elles sont idéales pour stocker des métadonnées, envoyer des
notifications ou déclencher des traitements asynchrones.

```php
use App\Models\Visite;
use App\Models\Evenement;
use App\Models\PluginMetadonnee;

/**
 * Appelé après l'enregistrement de chaque visite.
 *
 * @param  Visite  $visite   La visite qui vient d'être enregistrée
 * @param  array   $donnees  Données brutes du tracker
 * @param  mixed   $request  Illuminate\Http\Request
 */
public function apresVisite($visite, array $donnees, $request): void
{
    // Stocker une métadonnée calculée
    $score = $this->calculerScore($visite);

    PluginMetadonnee::enregistrer(
        visiteId: $visite->id,
        plugin:   $this->getIdentifiant(),
        cle:      'score',
        valeur:   $score
    );

    // Déclencher une action si un seuil est dépassé
    $seuil = (int) $this->config('seuil_alerte', 100);
    if (Visite::where('site_id', $visite->site_id)->count() >= $seuil) {
        // Votre logique d'alerte ici
    }
}

/**
 * Appelé après l'enregistrement de chaque événement personnalisé.
 *
 * @param  Evenement  $evenement   L'événement enregistré
 * @param  array      $donnees     Données brutes du tracker
 * @param  mixed      $request     Illuminate\Http\Request
 */
public function apresEvenement($evenement, array $donnees, $request): void
{
    // Réagir à un type d'événement spécifique
    if ($evenement->nom === 'paiement_complete') {
        PluginMetadonnee::enregistrer(
            visiteId: $evenement->visite_id,
            plugin:   $this->getIdentifiant(),
            cle:      'conversion',
            valeur:   true
        );
    }
}
```

---

### 7.7 Étendre le script tracker.js

Le script `tracker.js` est servi dynamiquement par l'application.
Le code JavaScript de chaque plugin actif est automatiquement injecté
dans ce script, disponible sur tous les sites trackés.

```php
/**
 * Retourne du JavaScript injecté dans tracker.js.
 *
 * Variables disponibles dans le contexte :
 *   - token    {string}   Token du site tracké
 *   - endpoint {string}   URL de l'endpoint POST /api/track
 *   - envoyer  {function} Fonction pour envoyer des données au serveur
 */
public function getTrackerJavaScript(): ?string
{
    return <<<'JS'

    // ─── Exemple : tracker la profondeur de défilement ────────────

    let profondeurMax = 0;

    window.addEventListener('scroll', function () {
        const hauteurScrollable = document.body.scrollHeight - window.innerHeight;
        if (hauteurScrollable <= 0) return;

        const profondeur = Math.round((window.scrollY / hauteurScrollable) * 100);
        if (profondeur > profondeurMax) {
            profondeurMax = profondeur;
        }
    });

    window.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden' && profondeurMax > 0) {
            envoyer({
                token   : token,
                type    : 'evenement',
                nom     : 'profondeur_scroll',
                chemin  : window.location.pathname,
                donnees : { profondeur: profondeurMax },
            });
        }
    });

    JS;
}
```

#### Exemples de mesures possibles

| Mesure | Événement JS à écouter |
|---|---|
| Profondeur de scroll | `scroll` |
| Temps de chargement | `load` → `performance.getEntriesByType('navigation')` |
| Clics sur des éléments | `click` avec sélecteur CSS |
| Soumissions de formulaire | `submit` |
| Copie de texte | `copy` |
| Sélection de texte | `selectionchange` |
| Inactivité | `setTimeout` avec reset sur `mousemove` |

> **Bonne pratique** : Regroupez les envois pour limiter les requêtes.
> Utilisez `visibilitychange` plutôt que `beforeunload` pour une
> meilleure compatibilité mobile.

---

### 7.8 Créer ses propres tables

Les migrations du plugin sont exécutées **automatiquement** lors de
l'activation. Elles sont situées dans `database/migrations/`.

```php
<?php
// plugins/MonPlugin/database/migrations/2024_01_01_000001_create_mon_plugin_data_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mon_plugin_data', function (Blueprint $table) {
            $table->id();

            // Référence vers le site concerné
            $table->foreignId('site_id')
                  ->constrained('sites')
                  ->cascadeOnDelete();

            // Vos colonnes
            $table->string('chemin', 1024);
            $table->integer('score')->default(0);
            $table->json('details')->nullable();

            $table->timestamp('cree_le')->useCurrent();

            // Index pour les requêtes fréquentes
            $table->index(['site_id', 'cree_le']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mon_plugin_data');
    }
};
```

#### Règles de nommage des tables

- Préfixez avec l'identifiant du plugin (remplacez `-` par `_`)
- Utilisez le snake_case
- Exemples :
  - Plugin `mon-plugin` → tables `mon_plugin_*`
  - Plugin `ai-analytics` → tables `ai_analytics_*`
  - Plugin `score-page` → tables `score_page_*`

#### Créer un modèle Eloquent

```php
<?php

namespace Plugins\MonPlugin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Site;

class MonPluginData extends Model
{
    protected $table = 'mon_plugin_data';

    public $timestamps = false;

    protected $fillable = ['site_id', 'chemin', 'score', 'details'];

    protected $casts = [
        'details'  => 'array',
        'cree_le'  => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
```

---

### 7.9 Stocker des métadonnées de visite

Pour attacher des données à une visite existante **sans modifier**
la table `visites` du core, utilisez la table `plugin_metadonnees`.

#### Écrire des métadonnées

```php
use App\Models\PluginMetadonnee;

// Valeur simple
PluginMetadonnee::enregistrer(
    visiteId: $visite->id,
    plugin:   'mon-plugin',
    cle:      'score',
    valeur:   85
);

// Tableau (sérialisé automatiquement en JSON)
PluginMetadonnee::enregistrer(
    visiteId: $visite->id,
    plugin:   'mon-plugin',
    cle:      'details',
    valeur:   [
        'categorie' => 'power-user',
        'source'    => 'organique',
    ]
);
```

#### Lire des métadonnées

```php
use App\Models\Visite;

$visite = Visite::with('metadonnees')->find($visiteId);

// Toutes les métadonnées de votre plugin pour cette visite
$mesMeta = $visite->metadonnees
    ->where('plugin', 'mon-plugin')
    ->keyBy('cle');

$score = $mesMeta['score']?->valeur;

// Ou directement en base
$score = \App\Models\PluginMetadonnee::where('visite_id', $visite->id)
    ->where('plugin', 'mon-plugin')
    ->where('cle', 'score')
    ->value('valeur');
```

---

### 7.10 Assets et CSS personnalisés

#### Option A — Utiliser Tailwind CSS du core (recommandé)

Le fichier `tailwind.config.js` scanne automatiquement les vues de
tous les plugins :

```javascript
content: [
    // ...
    './plugins/*/resources/views/**/*.blade.php',
],
```

Utilisez directement les classes Tailwind dans vos vues. Après
l'installation d'un nouveau plugin, l'administrateur doit lancer :

```bash
npm run build
```

**C'est la méthode recommandée.** Elle garantit la cohérence visuelle
avec le reste de l'application.

#### Option B — CSS précompilé

Si votre plugin nécessite des styles impossibles à exprimer avec
Tailwind, placez un fichier CSS précompilé dans :

```
plugins/MonPlugin/resources/assets/style.css
```

Ce fichier est automatiquement :
1. Copié vers `public/plugins/mon-plugin/style.css` lors de l'activation
2. Inclus dans le `<head>` de toutes les pages quand le plugin est actif

Vous n'avez rien d'autre à configurer.

---

## 8. Composants UI disponibles

Utilisez ces composants dans vos vues pour maintenir une interface
cohérente avec le reste de LaraMetrics.

### `<x-card>` — Carte

```blade
{{-- Carte simple --}}
<x-card>
    <p class="text-sm text-gray-600">Contenu.</p>
</x-card>

{{-- Avec titre dans l'en-tête --}}
<x-card titre="Statistiques">
    <p class="text-sm text-gray-600">Contenu.</p>
</x-card>

{{-- Sans padding interne (idéal pour les tableaux) --}}
<x-card titre="Liste" :padding="false">
    <table class="w-full">...</table>
</x-card>
```

---

### `<x-button>` — Bouton

```blade
{{-- Variantes --}}
<x-button>Annuler</x-button>
<x-button variant="primary">Sauvegarder</x-button>
<x-button variant="danger">Supprimer</x-button>

{{-- Lien (génère un <a> au lieu d'un <button>) --}}
<x-button variant="primary" href="{{ route('plugin.mon-plugin.index') }}">
    Voir la page
</x-button>

{{-- Tailles --}}
<x-button size="sm">Petit</x-button>
<x-button size="md">Normal (défaut)</x-button>
<x-button size="lg">Grand</x-button>

{{-- Avec icône --}}
<x-button variant="primary">
    <x-icon name="plus" class="w-4 h-4" />
    Ajouter
</x-button>

{{-- Désactivé avec Alpine.js --}}
<x-button variant="primary" x-bind:disabled="chargement">
    <span x-text="chargement ? 'En cours...' : 'Confirmer'"></span>
</x-button>
```

---

### `<x-icon>` — Icône

Le composant utilise la bibliothèque [Heroicons](https://heroicons.com/)
(style Outline). Passez directement le nom de l'icône Heroicons.

```blade
{{-- Utilisation de base --}}
<x-icon name="chart-bar" class="w-5 h-5" />
<x-icon name="users" class="w-5 h-5 text-gray-500" />
<x-icon name="check" class="w-4 h-4 text-green-600" />
<x-icon name="x-mark" class="w-4 h-4 text-red-600" />
<x-icon name="arrow-path" class="w-5 h-5 animate-spin" />

{{-- Animation --}}
<x-icon name="arrow-path" class="w-5 h-5 text-gray-400 animate-spin" />
```

#### Raccourcis prédéfinis

Certains noms courts sont mappés vers leur nom Heroicons complet :

| Raccourci | Icône réelle |
|---|---|
| `globe` | `globe-alt` |
| `cog` | `cog-6-tooth` |
| `cpu` | `cpu-chip` |
| `clipboard` | `clipboard-document` |
| `puzzle` | `puzzle-piece` |
| `cursor-click` | `cursor-arrow-rays` |
| `code` | `code-bracket` |
| `document` | `document-text` |
| `device-phone` | `device-phone-mobile` |
| `computer` | `computer-desktop` |

Pour tout autre icône, utilisez son nom Heroicons directement.
Consultez [heroicons.com](https://heroicons.com/) pour la liste complète.

---

### `<x-input>` — Champ de formulaire

```blade
{{-- Champ simple --}}
<x-input
    name="email"
    label="Adresse e-mail"
    type="email"
    :required="true"
    placeholder="exemple@mail.fr"
    :value="old('email')"
/>

{{-- Avec texte d'aide --}}
<x-input
    name="cle_api"
    label="Clé API"
    type="password"
    :required="true"
    aide="Votre clé sera chiffrée avant stockage."
/>
```

Les erreurs de validation sont affichées automatiquement sous le champ
grâce à la directive `@error`.

---

### `<x-stats-card>` — Carte de statistique

```blade
{{-- Valeur statique --}}
<x-stats-card titre="Total" valeur="1 234" icon="chart-bar" />

{{-- Valeur dynamique avec Alpine.js --}}
<x-stats-card titre="Visiteurs" icon="users">
    <x-slot:valeur>
        <span x-text="stats?.total ?? 0"></span>
    </x-slot:valeur>
</x-stats-card>
```

---

### Patron Alpine.js recommandé pour les onglets

```blade
<div x-data="monPlugin()" x-init="charger()">

    {{-- Chargement --}}
    <div x-show="chargement" class="flex items-center justify-center py-12">
        <x-icon name="arrow-path" class="w-5 h-5 text-gray-400 animate-spin" />
        <span class="ml-2 text-sm text-gray-500">Chargement...</span>
    </div>

    {{-- Erreur --}}
    <div x-show="erreur" class="bg-red-50 border border-red-200 rounded p-3 mb-4">
        <p class="text-sm text-red-700" x-text="erreur"></p>
    </div>

    {{-- Contenu --}}
    <div x-show="!chargement && !erreur" x-cloak>
        {{-- Votre contenu ici --}}
    </div>

</div>

<script>
function monPlugin() {
    return {
        chargement : true,
        erreur     : null,
        donnees    : null,

        async charger() {
            this.chargement = true;
            this.erreur     = null;

            try {
                const r = await fetch('/plugins/mon-plugin/donnees', {
                    headers: {
                        'Accept'       : 'application/json',
                        'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                if (!r.ok) throw new Error('Erreur serveur : ' + r.status);

                this.donnees = await r.json();

            } catch (e) {
                this.erreur = e.message;
            } finally {
                this.chargement = false;
            }
        },
    }
}
</script>
```

---

## 9. Cycle de vie d'un plugin

```
╔══════════════════════════════════════════════════════════╗
║                   INSTALLATION                           ║
║                                                          ║
║  1. Copier le dossier dans /plugins/                     ║
║  2. Le plugin est "découvert" au prochain démarrage      ║
║  3. Il apparaît dans la liste avec le statut "Inactif"   ║
╚══════════════════════════════════════════════════════════╝
                          │
                    Clic "Activer"
                          │
                          ▼
╔══════════════════════════════════════════════════════════╗
║                   ACTIVATION                             ║
║                                                          ║
║  plugin->activer()                                       ║
║    ├─ Migrations exécutées (database/migrations/)        ║
║    └─ Assets copiés (resources/assets/ → public/)        ║
║                                                          ║
║  Base de données mise à jour : actif = true              ║
║  Cache invalidé                                          ║
╚══════════════════════════════════════════════════════════╝
                          │
              À chaque démarrage de l'app
                          │
                          ▼
╔══════════════════════════════════════════════════════════╗
║                   EN FONCTIONNEMENT                      ║
║                                                          ║
║  plugin->enregistrer()                                   ║
║    ├─ Routes enregistrées dans Laravel                   ║
║    ├─ Namespace de vues enregistré                       ║
║    └─ Assets publiés si absent                           ║
║                                                          ║
║  Hooks, onglets, nav items collectés                     ║
║  Plugin disponible dans toute l'application              ║
╚══════════════════════════════════════════════════════════╝
                          │
                   Clic "Désactiver"
                          │
                          ▼
╔══════════════════════════════════════════════════════════╗
║                   DÉSACTIVATION                          ║
║                                                          ║
║  plugin->desactiver()                                    ║
║    └─ Votre logique de nettoyage                         ║
║                                                          ║
║  Base de données mise à jour : actif = false             ║
║  Cache invalidé                                          ║
║  Plugin ignoré aux prochains démarrages                  ║
╚══════════════════════════════════════════════════════════╝
```

### Surcharger les méthodes du cycle de vie

```php
/**
 * Appelé à l'activation. Les migrations sont exécutées par parent::activer().
 */
public function activer(): void
{
    parent::activer(); // NE PAS OUBLIER : exécute migrations + assets

    // Initialisation spécifique au plugin
    // Exemple : insérer des données par défaut
    \DB::table('mon_plugin_config')->insertOrIgnore([
        'cle'    => 'version_schema',
        'valeur' => '1',
    ]);
}

/**
 * Appelé à la désactivation.
 */
public function desactiver(): void
{
    // Nettoyage des caches spécifiques au plugin
    \Cache::forget('mon_plugin_stats');
}

/**
 * Appelé à la désinstallation. Les assets sont supprimés par parent::desinstaller().
 */
public function desinstaller(): void
{
    parent::desinstaller(); // Supprime les assets de public/

    // Optionnel : supprimer les tables du plugin
    // À utiliser avec précaution (perte de données irréversible)
    // \Schema::dropIfExists('mon_plugin_data');
}

/**
 * Appelé à chaque démarrage. Les routes et vues sont enregistrées par parent::enregistrer().
 */
public function enregistrer(): void
{
    parent::enregistrer(); // NE PAS OUBLIER : routes + vues + assets

    // Enregistrements supplémentaires si nécessaire
}
```

---

## 10. Conventions et bonnes pratiques

### Code PHP

- Suivre le standard **PSR-12**
- Typer tous les paramètres et valeurs de retour
- Documenter les méthodes publiques avec des **PHPDoc**
- Toujours appeler `parent::activer()` et `parent::enregistrer()` si vous
  surchargez ces méthodes
- Entourer les appels dans `apresVisite()` et `apresEvenement()` d'un
  `try/catch` — une erreur dans un plugin ne doit jamais bloquer le tracking

```php
// ✓ Correct
public function apresVisite($visite, array $donnees, $request): void
{
    try {
        $this->traiterVisite($visite);
    } catch (\Throwable $e) {
        \Log::error("MonPlugin::apresVisite : " . $e->getMessage());
    }
}

// ✗ Incorrect — une exception non gérée bloque le tracking
public function apresVisite($visite, array $donnees, $request): void
{
    $this->traiterVisite($visite); // Peut lever une exception
}
```

### Base de données

- **Toujours** préfixer les tables avec l'identifiant du plugin
- Ne **jamais** modifier les tables du core (`visites`, `sites`, `evenements`, etc.)
- Utiliser `plugin_metadonnees` pour les données liées aux visites
- Créer des index sur les colonnes fréquemment interrogées
- Utiliser `cascadeOnDelete()` sur les clés étrangères vers `sites`

### Interface

- Utiliser **exclusivement** les composants du core pour les éléments UI
  (`<x-card>`, `<x-button>`, `<x-icon>`, `<x-input>`)
- **Ne pas** utiliser d'emoji comme icônes — utiliser Heroicons
- Rendre toutes les vues **responsives** (mobile-first)
- Utiliser **Alpine.js** pour l'interactivité côté client
- **Ne pas** importer de framework JavaScript lourd (React, Vue, Angular)
- Pour Chart.js, il est disponible globalement via `window.Chart`

### Sécurité

- Valider **toutes** les entrées utilisateur dans les controllers
- Utiliser Eloquent ou le Query Builder (requêtes préparées)
- Ne **jamais** stocker de clés API ou mots de passe en clair
- Ne **pas** désactiver le middleware `auth` sur les routes du plugin
- Éviter `eval()` et les inclusions dynamiques de fichiers

### Performance

- Mettre en cache les résultats coûteux avec `Cache::remember()`
- Éviter les requêtes N+1 (utiliser `with()` pour le chargement eager)
- Garder `enrichirTracking()` rapide — pas de requêtes en base ou appels API
- Utiliser des jobs en arrière-plan pour les traitements longs dans `apresVisite()`

---

## 11. Exemple complet : plugin ScorePage

Ce plugin calcule et affiche un score de qualité pour chaque page visitée,
basé sur le taux de rebond et la durée de session.

### Structure

```
plugins/
└── ScorePage/
    ├── manifest.json
    ├── src/
    │   ├── ScorePagePlugin.php
    │   └── Http/
    │       └── Controllers/
    │           └── ScoreController.php
    ├── database/
    │   └── migrations/
    │       └── 2024_01_01_000001_create_score_page_scores_table.php
    ├── resources/
    │   └── views/
    │       ├── tab.blade.php
    │       └── page.blade.php
    └── routes/
        └── web.php
```

### manifest.json

```json
{
    "identifiant": "score-page",
    "nom": "Score de Page",
    "version": "1.0.0",
    "description": "Calcule un score de qualité pour chaque page visitée.",
    "auteur": "LaraMetrics",
    "licence": "MIT",
    "classe": "Plugins\\ScorePage\\ScorePagePlugin",
    "hooks": ["tab.scores"]
}
```

### ScorePagePlugin.php

```php
<?php

namespace Plugins\ScorePage;

use App\Core\Plugin\AbstractPlugin;
use App\Models\PluginMetadonnee;
use Illuminate\Support\Facades\Log;

class ScorePagePlugin extends AbstractPlugin
{
    public function getIdentifiant(): string
    {
        return 'score-page';
    }

    public function getNom(): string
    {
        return 'Score de Page';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getOnglets(): array
    {
        return [
            [
                'id'    => 'scores',
                'label' => 'Scores',
                'icone' => 'star',
            ],
        ];
    }

    public function getHooks(): array
    {
        return ['tab.scores'];
    }

    public function rendrePourHook(string $hook, array $donnees = []): string
    {
        return match ($hook) {
            'tab.scores' => view('score-page::tab', $donnees)->render(),
            default      => '',
        };
    }

    public function getNavigationItems(): array
    {
        return [
            [
                'label' => 'Scores',
                'route' => 'plugin.score-page.index',
                'icone' => 'star',
            ],
        ];
    }

    public function getReglages(): array
    {
        return [
            [
                'cle'         => 'penalite_rebond',
                'label'       => 'Pénalité rebond',
                'type'        => 'text',
                'placeholder' => '30',
                'aide'        => 'Points retirés si la visite est un rebond.',
            ],
            [
                'cle'         => 'duree_minimale',
                'label'       => 'Durée minimale (secondes)',
                'type'        => 'text',
                'placeholder' => '10',
                'aide'        => 'Durée en dessous de laquelle une pénalité est appliquée.',
            ],
        ];
    }

    /**
     * Calculer et stocker le score après chaque visite.
     */
    public function apresVisite($visite, array $donnees, $request): void
    {
        try {
            $score = $this->calculerScore($visite);

            PluginMetadonnee::enregistrer(
                visiteId: $visite->id,
                plugin:   $this->getIdentifiant(),
                cle:      'score',
                valeur:   $score
            );
        } catch (\Throwable $e) {
            Log::error("ScorePage::apresVisite : " . $e->getMessage());
        }
    }

    /**
     * Injecter du JS pour mesurer la performance de chargement.
     */
    public function getTrackerJavaScript(): ?string
    {
        return <<<'JS'

        window.addEventListener('load', function () {
            const perf = performance.getEntriesByType('navigation')[0];
            if (!perf) return;

            envoyer({
                token   : token,
                type    : 'evenement',
                nom     : 'performance_chargement',
                chemin  : window.location.pathname,
                donnees : {
                    ttfb   : Math.round(perf.responseStart - perf.requestStart),
                    dom    : Math.round(perf.domContentLoadedEventEnd - perf.startTime),
                    charge : Math.round(perf.loadEventEnd - perf.startTime),
                },
            });
        });

        JS;
    }

    /**
     * Calculer un score de 0 à 100.
     */
    private function calculerScore($visite): int
    {
        $score = 100;

        $penaliteRebond  = (int) $this->config('penalite_rebond', 30);
        $dureeMinimale   = (int) $this->config('duree_minimale', 10);

        if ($visite->est_rebond) {
            $score -= $penaliteRebond;
        }

        if ($visite->duree_session !== null && $visite->duree_session < $dureeMinimale) {
            $score -= 20;
        }

        if (empty($visite->titre)) {
            $score -= 10;
        }

        return max(0, min(100, $score));
    }
}
```

### routes/web.php

```php
<?php

use Plugins\ScorePage\Http\Controllers\ScoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ScoreController::class, 'index'])->name('index');
Route::get('/scores', [ScoreController::class, 'scores'])->name('scores');
```

### ScoreController.php

```php
<?php

namespace Plugins\ScorePage\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PluginMetadonnee;
use App\Models\Visite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function index()
    {
        return view('score-page::page');
    }

    public function scores(): JsonResponse
    {
        $scores = PluginMetadonnee::where('plugin', 'score-page')
            ->where('cle', 'score')
            ->join('visites', 'plugin_metadonnees.visite_id', '=', 'visites.id')
            ->select(
                'visites.chemin',
                DB::raw('AVG(CAST(plugin_metadonnees.valeur AS UNSIGNED)) as moyenne'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('visites.chemin')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        return response()->json($scores);
    }
}
```

### tab.blade.php

```blade
{{-- plugins/ScorePage/resources/views/tab.blade.php --}}

<div x-data="scoresTab()" x-init="charger()">

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <x-stats-card titre="Pages analysées" icon="document">
            <x-slot:valeur>
                <span x-text="donnees?.length ?? 0"></span>
            </x-slot:valeur>
        </x-stats-card>
        <x-stats-card titre="Score moyen" icon="star">
            <x-slot:valeur>
                <span x-text="scoreMoyen() + '/100'"></span>
            </x-slot:valeur>
        </x-stats-card>
        <x-stats-card titre="Pages critiques" icon="exclamation">
            <x-slot:valeur>
                <span x-text="(donnees ?? []).filter(p => p.moyenne < 40).length"></span>
            </x-slot:valeur>
        </x-stats-card>
    </div>

    <x-card titre="Score par page" :padding="false">

        <div x-show="chargement" class="flex items-center justify-center py-8">
            <x-icon name="arrow-path" class="w-5 h-5 text-gray-400 animate-spin" />
            <span class="ml-2 text-sm text-gray-500">Chargement...</span>
        </div>

        <table class="w-full text-sm" x-show="!chargement">
            <thead>
                <tr class="border-b border-gray-200 text-left">
                    <th class="px-4 py-2 text-xs font-medium text-gray-500">Page</th>
                    <th class="px-4 py-2 text-xs font-medium text-gray-500 text-right">Visites</th>
                    <th class="px-4 py-2 text-xs font-medium text-gray-500 text-right">Score moyen</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="page in donnees ?? []" :key="page.chemin">
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-2 text-gray-900" x-text="page.chemin"></td>
                        <td class="px-4 py-2 text-gray-600 text-right" x-text="page.total"></td>
                        <td class="px-4 py-2 text-right font-medium">
                            <span
                                :class="{
                                    'text-green-700'  : page.moyenne >= 70,
                                    'text-yellow-700' : page.moyenne >= 40 && page.moyenne < 70,
                                    'text-red-700'    : page.moyenne < 40,
                                }"
                                x-text="Math.round(page.moyenne) + '/100'"
                            ></span>
                        </td>
                    </tr>
                </template>
                <template x-if="!chargement && !donnees?.length">
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-400 text-sm">
                            Aucune donnée disponible
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

    </x-card>
</div>

<script>
function scoresTab() {
    return {
        chargement : true,
        erreur     : null,
        donnees    : null,

        async charger() {
            try {
                const r = await fetch('/plugins/score-page/scores', {
                    headers: {
                        'Accept'       : 'application/json',
                        'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                if (!r.ok) throw new Error('Erreur ' + r.status);
                this.donnees = await r.json();
            } catch (e) {
                this.erreur = e.message;
            } finally {
                this.chargement = false;
            }
        },

        scoreMoyen() {
            if (!this.donnees?.length) return 0;
            const total = this.donnees.reduce((s, p) => s + parseFloat(p.moyenne), 0);
            return Math.round(total / this.donnees.length);
        },
    }
}
</script>
```

---

## 12. Référence API complète

### Méthodes de `PluginInterface`

| Méthode | Retour | Obligatoire | Description |
|---|---|---|---|
| `getIdentifiant()` | `string` | ✅ | Identifiant unique en kebab-case |
| `getNom()` | `string` | ✅ | Nom lisible affiché dans l'UI |
| `getVersion()` | `string` | ✅ | Version au format semver |
| `activer()` | `void` | — | Logique d'activation (migrations, init) |
| `desactiver()` | `void` | — | Logique de désactivation |
| `desinstaller()` | `void` | — | Logique de désinstallation |
| `enregistrer()` | `void` | — | Enregistre routes, vues, assets |
| `getOnglets()` | `array` | — | Onglets ajoutés au dashboard |
| `getReglages()` | `array` | — | Champs de configuration |
| `getNavigationItems()` | `array` | — | Liens dans la navigation |
| `getHooks()` | `string[]` | — | Hooks visuels utilisés |
| `enrichirTracking()` | `array\|null` | — | Modifie les données avant stockage |
| `apresVisite()` | `void` | — | Réagit après une visite |
| `apresEvenement()` | `void` | — | Réagit après un événement |
| `getTrackerJavaScript()` | `string\|null` | — | JS injecté dans tracker.js |
| `getCssPath()` | `string\|null` | — | Chemin CSS (géré par AbstractPlugin) |
| `getManifest()` | `array` | — | Données du manifest.json |

### Helpers d'`AbstractPlugin`

| Helper | Description |
|---|---|
| `$this->config(string $cle, mixed $defaut = null)` | Lire une valeur de configuration sauvegardée |
| `$this->chemin(string $relatif = '')` | Chemin absolu vers un fichier du plugin |

### Modèles du core accessibles

| Classe | Table | Description |
|---|---|---|
| `App\Models\Site` | `sites` | Sites trackés |
| `App\Models\Visite` | `visites` | Pages vues enregistrées |
| `App\Models\Evenement` | `evenements` | Événements personnalisés |
| `App\Models\Plugin` | `plugins` | État des plugins installés |
| `App\Models\PluginMetadonnee` | `plugin_metadonnees` | Métadonnées par visite |

### Format des structures de données

#### Onglet (`getOnglets()`)

```php
[
    'id'    => string,   // Identifiant unique, utilisé comme nom de hook "tab.{id}"
    'label' => string,   // Texte affiché dans l'onglet
    'icone' => string,   // Nom d'icône Heroicons
]
```

#### Réglage (`getReglages()`)

```php
[
    'cle'         => string,        // Clé de stockage en base
    'label'       => string,        // Label du champ
    'type'        => string,        // 'text' | 'password' | 'select' | 'textarea'
    'obligatoire' => bool,          // Affiche * si true (défaut : false)
    'placeholder' => string,        // Texte placeholder (optionnel)
    'aide'        => string,        // Texte d'aide sous le champ (optionnel)
    'options'     => string[],      // Requis si type === 'select'
]
```

#### Lien de navigation (`getNavigationItems()`)

```php
[
    'label' => string,   // Texte du lien
    'route' => string,   // Nom de route Laravel (ex: 'plugin.mon-plugin.index')
    'icone' => string,   // Nom d'icône Heroicons (optionnel)
]
```

---

## 13. Dépannage

### Le plugin n'apparaît pas dans la liste

Vérifiez les points suivants dans l'ordre :

```bash
# 1. Le dossier du plugin est-il en PascalCase ?
ls plugins/

# 2. Le manifest.json est-il valide ?
php -r "echo json_encode(json_decode(file_get_contents('plugins/MonPlugin/manifest.json')));"

# 3. La classe est-elle accessible ?
php artisan tinker
>>> class_exists('Plugins\MonPlugin\MonPlugin')

# 4. Consulter les logs
tail -f storage/logs/laravel.log
```

### Erreur "Class not found"

```
Cause probable : Le nom du dossier ne correspond pas au namespace.

Namespace : Plugins\MonPlugin\...
Dossier requis : plugins/MonPlugin/      ← PascalCase
Dossier actuel : plugins/mon-plugin/     ← kebab-case ✗

Solution : Renommer le dossier en PascalCase.
```

### Les routes du plugin retournent 404

```
Causes possibles :
1. Le plugin n'est pas actif en base de données
2. Le fichier routes/web.php est absent ou vide
3. Les noms de routes ne correspondent pas

Vérification :
php artisan route:list | grep "mon-plugin"
```

### Les migrations ne s'exécutent pas

```
Causes possibles :
1. Le dossier database/migrations/ est absent
2. Le fichier de migration a un nom invalide

Format correct : YYYY_MM_DD_HHMMSS_nom_descriptif.php
Exemple : 2024_01_15_120000_create_mon_plugin_data_table.php

Vérification manuelle :
php artisan migrate --path=plugins/MonPlugin/database/migrations
```

### Les vues du plugin sont introuvables

```
Erreur : View [mon-plugin::tab] not found.

Causes possibles :
1. Le dossier resources/views/ est absent dans le plugin
2. La vue tab.blade.php n'existe pas
3. Le plugin n'est pas actif

Le namespace de vues est enregistré lors de enregistrer().
Si le plugin est inactif, ses vues ne sont pas disponibles.
```

### Les assets CSS ne sont pas chargés

```
Causes possibles :
1. Le fichier resources/assets/style.css est absent
2. Le dossier public/plugins/mon-plugin/ n'a pas été créé

Solution : Désactiver puis réactiver le plugin pour forcer
la copie des assets, ou créer le dossier manuellement :

mkdir -p public/plugins/mon-plugin
cp plugins/MonPlugin/resources/assets/style.css \
   public/plugins/mon-plugin/style.css
```

---

*Documentation LaraMetrics — Système de Plugins v1.0.0*
```
