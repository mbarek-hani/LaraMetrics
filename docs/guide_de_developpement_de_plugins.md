# Guide de Développement de Plugins LaraMetrics

> **Version LaraMetrics** : Laravel 13 · Alpine.js · Tailwind CSS
> **Objectif** : Étendre les fonctionnalités de LaraMetrics sans modifier son code source (*core*).

LaraMetrics repose sur une architecture modulaire robuste. Le **core** gère le tracking, l'authentification et l'interface principale, tandis que toutes les fonctionnalités spécifiques ou expérimentales peuvent être implémentées via des **plugins**.

---

## Table des matières

1. [Démarrage Rapide](#1-️-démarrage-rapide)
2. [Architecture d'un Plugin](#2--architecture-dun-plugin)
3. [Le fichier `manifest.json`](#3-️-le-fichier-manifestjson)
4. [La Classe Principale (Core API)](#4--la-classe-principale-core-api)
5. [Étendre l'Interface Utilisateur (UI)](#5--étendre-linterface-utilisateur-ui)
6. [Manipuler le Tracking et les Données](#6--manipuler-le-tracking-et-les-données)
7. [Base de Données et Métadonnées](#7--base-de-données-et-métadonnées)
8. [Composants UI Disponibles](#8--composants-ui-disponibles)
9. [Cycle de Vie d'un Plugin](#9--cycle-de-vie-dun-plugin)

---

## 1. Démarrage Rapide

La méthode la plus simple pour démarrer le développement d'un plugin est d'utiliser l'interface en ligne de commande de Laravel (Artisan) incluse avec LaraMetrics.

### Créer un nouveau plugin

Ouvrez votre terminal à la racine du projet et tapez :

```bash
php artisan make:plugin MonPlugin
```

> [!TIP]
> **Que fait cette commande ?**  
> Elle génère l'arborescence complète dans `plugins/MonPlugin/`, incluant votre classe PHP `MonPlugin.php`, le fichier `manifest.json`, un fichier de routes `web.php` et les répertoires nécessaires (vues, migrations).

### Activer le plugin

1. Rendez-vous dans le tableau de bord LaraMetrics, section **Plugins**.
2. Trouvez "MonPlugin" dans la liste et cliquez sur **Activer**.

### Supprimer un plugin

Si vous souhaitez désinstaller un plugin et purger ses fichiers du système, utilisez :

```bash
php artisan remove:plugin MonPlugin
```
> [!WARNING]  
> Cette action supprimera définitivement le code source de ce plugin.

---

## 2. Architecture d'un Plugin

Une fois généré, un plugin possède une arborescence standardisée (conforme à la norme PSR-4 via un autoloader dynamique interne) :

```text
plugins/
└── MonPlugin/                              ← PascalCase obligatoire
    ├── manifest.json                       ← Fichier de déclaration
    ├── src/                                ← Code source PHP
    │   ├── MonPlugin.php                   ← Classe principale
    │   ├── Http/Controllers/               ← (Optionnel) Contrôleurs
    │   ├── Services/                       ← (Optionnel) Services
    │   └── Models/                         ← (Optionnel) Modèles Eloquent
    ├── database/migrations/                ← (Optionnel) Migrations DB
    ├── resources/
    │   ├── views/                          ← (Optionnel) Vues Blade
    │   └── assets/                         ← (Optionnel) Fichiers statiques (CSS)
    └── routes/
        └── web.php                         ← (Optionnel) Fichier de routage
```

---

## 3. Le fichier `manifest.json`

Le fichier `manifest.json` est la carte d'identité de votre plugin. Il est lu par LaraMetrics au démarrage pour "découvrir" votre plugin.

```json
{
    "identifiant": "mon-plugin",
    "nom": "Mon Plugin",
    "version": "1.0.0",
    "description": "Ajoute des fonctionnalités d'analyse avancées.",
    "auteur": "John Doe",
    "licence": "MIT",
    "classe": "Plugins\\MonPlugin\\MonPlugin",
    "hooks": [
        "tab.mon-plugin"
    ]
}
```

**Champs critiques :**
- `identifiant` : Toujours en `kebab-case`. Doit être unique.
- `classe` : Le namespace PHP de votre classe principale. L'autoloader l'utilise pour trouver `src/MonPlugin.php`.

---

## 4. La Classe Principale (Core API)

La classe principale de votre plugin (ex: `MonPlugin.php`) doit obligatoirement hériter de `App\Core\Plugin\AbstractPlugin`.

L'héritage d'`AbstractPlugin` vous fournit gratuitement :
- L'enregistrement automatique des **routes** (préfixe `plugins.mon-plugin.`).
- L'enregistrement du **namespace de vues** (ex: `mon-plugin::ma-vue`).
- La **publication des assets** vers le répertoire public de Laravel.
- L'exécution de vos **migrations** lors de l'activation.

### Structure Minimale

```php
<?php

namespace Plugins\MonPlugin;

use App\Core\Plugin\AbstractPlugin;

class MonPlugin extends AbstractPlugin
{
    public function getIdentifiant(): string { return 'mon-plugin'; }
    public function getNom(): string { return 'Mon Plugin'; }
    public function getVersion(): string { return '1.0.0'; }
}
```

---

## 5. Étendre l'Interface Utilisateur (UI)

Vous pouvez greffer des éléments visuels à LaraMetrics directement depuis votre classe principale.

### 5.1 Ajouter un lien dans le menu de navigation

Surchargez la méthode `getNavigationItems` :

```php
public function getNavigationItems(): array
{
    return [
        [
            'label' => 'Analyses Pro',
            'route' => 'plugins.mon-plugin.index', // La route définie dans votre web.php
            'icone' => 'chart-bar',                // Icône Heroicons
        ],
    ];
}
```

### 5.2 Créer une page de réglages dédiée

Chaque plugin bénéficie d'une section isolée dans la page "Réglages" de LaraMetrics. Déclarez vos champs :

```php
public function getReglages(): array
{
    return [
        [
            'cle'         => 'api_key',
            'label'       => 'Clé API Externe',
            'type'        => 'password',
            'obligatoire' => true,
            'aide'        => 'Nécessaire pour interroger le service tiers.',
        ],
    ];
}
```
*Pour récupérer cette valeur dans votre plugin, utilisez le helper interne : `$this->config('api_key')`.*

### 5.3 Insérer du contenu via les Hooks visuels

Les hooks (ex: `tab.{id}` (où `{id}` correspond à l'identifiant d'un onglet déclaré via la méthode `getOnglets()`)) permettent d'injecter du HTML sans modifier les fichiers Blade du Core.

```php
// 1. Déclarer l'utilisation du hook
public function getHooks(): array
{
    return ['tab.mon-plugin'];
}

// 2. Rendre le contenu lorsqu'il est appelé
public function rendrePourHook(string $hook, array $donnees = []): string
{
    if ($hook === 'tab.mon-plugin') {
        return view('mon-plugin::tab', $donnees)->render();
    }
    return '';
}
```

---

## 6. Manipuler le Tracking et les Données

La véritable puissance des plugins LaraMetrics réside dans la manipulation du pipeline de tracking.

### 6.1 Modifier ou filtrer les données (Avant Enregistrement)

Appelée à chaque requête de tracking entrante, `enrichirTracking` vous permet d'altérer les données brutes avant qu'elles ne soient écrites en base.

```php
public function enrichirTracking(array $donnees, $request): ?array
{
    // Filtrer les IPs internes en retournant null (annule le tracking)
    if ($request->ip() === '127.0.0.1') {
        return null; 
    }

    // Ajouter une donnée personnalisée
    $donnees['heure_traitement'] = now()->toTimeString();

    return $donnees;
}
```

### 6.2 Réagir à un événement (Après Enregistrement)

Déclenchez une action asynchrone, envoyez un email ou stockez une métadonnée dès qu'une visite ou un événement a eu lieu :

```php
public function apresVisite($visite, array $donnees, $request): void
{
    // Exemple : si le paramètre UTM source est "newsletter"
    if (($donnees['utm_source'] ?? '') === 'newsletter') {
        \Log::info("Visite via newsletter reçue !");
    }
}
```

### 6.3 Injecter du JavaScript dans le Tracker Client

Le fichier `tracker.js` est servi à vos clients. Vous pouvez y injecter des scripts additionnels.

```php
public function getTrackerJavaScript(): ?string
{
    return <<<'JS'
    // Exemple : suivre la profondeur de scroll
    window.addEventListener('scroll', function () {
        // Logique de tracking client...
        envoyer({
            token: token,
            type: 'evenement',
            nom: 'scroll',
            donnees: { /* ... */ }
        });
    });
    JS;
}
```

---

## 7. Base de Données et Métadonnées

### Tables personnalisées

Si vous placez des fichiers Laravel Migrations dans `plugins/MonPlugin/database/migrations/`, elles seront exécutées lors du clic sur le bouton **Activer**.
> [!NOTE]
> Convention : Préfixez toujours vos tables avec l'identifiant du plugin (ex: `mon_plugin_logs`) pour éviter tout conflit avec le Core.

### Les Métadonnées de Visite

Si vous souhaitez lier une petite information spécifique à une Visite existante sans créer de nouvelle table, utilisez le modèle `PluginMetadonnee` fourni par le Core :

```php
use App\Models\PluginMetadonnee;

// Sauvegarder la métadonnée
PluginMetadonnee::enregistrer(
    visiteId: $visite->id,
    plugin:   $this->getIdentifiant(),
    cle:      'score_engagement',
    valeur:   85
);
```

---

## 8. Composants UI Disponibles

Pour que votre plugin s'intègre parfaitement au design de LaraMetrics, utilisez nos composants Blade Tailwind/Alpine prêts à l'emploi dans vos fichiers `resources/views/` :

| Composant | Utilisation |
|---|---|
| **Carte** | `<x-card titre="Statistiques"> Contenu... </x-card>` |
| **Bouton** | `<x-button variant="primary"> Valider </x-button>` |
| **Icône** | `<x-icon name="chart-bar" class="w-5 h-5 text-gray-500" />` (utilise Heroicons) |
| **Input** | `<x-input name="email" label="Email" type="email" required />` |
| **Stats** | `<x-stats-card titre="Total" valeur="42" icon="users" />` |

> [!TIP]
> LaraMetrics utilise **Tailwind CSS**. Les classes utilisées dans vos vues Blade (`plugins/*/resources/views/**/*.blade.php`) sont automatiquement analysées et compilées par Vite (via `npm run build`).

---

## 9. Cycle de Vie d'un Plugin

Afin de gérer des états complexes (création de données initiales, suppression de tables), vous pouvez surcharger les méthodes du cycle de vie depuis votre classe principale.

```php
/**
 * Exécutée lors du clic sur "Activer".
 */
public function activer(): void
{
    parent::activer(); // Essentiel : exécute les migrations et publie les assets CSS/JS
    // Votre code d'initialisation (ex: insérer des réglages par défaut)
}

/**
 * Exécutée lors du clic sur "Désactiver".
 */
public function desactiver(): void
{
    parent::desactiver();
    // Votre code de mise en pause (ex: vider le cache du plugin)
}

/**
 * Exécutée lors de la suppression du plugin.
 */
public function desinstaller(): void
{
    parent::desinstaller(); // Essentiel : supprime les assets publiés
    // Votre code de nettoyage (ex: Optionnel, Drop de vos tables personnalisées)
}
```

---

**Bon développement avec LaraMetrics !**
