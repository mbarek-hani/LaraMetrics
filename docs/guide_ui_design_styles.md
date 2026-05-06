# Guide UI, Design & Styles

> **Technologies** : Vanilla CSS (BEM) · Alpine.js · CSS Variables

Flux a migré d'un framework utilitaire (Tailwind CSS) vers une architecture **Vanilla CSS** basée sur la méthodologie **BEM** (Block Element Modifier). Ce guide explique comment concevoir et styliser vos plugins pour qu'ils s'intègrent nativement au design du Core.

---

## 1. Architecture CSS (BEM)

Toutes les classes CSS de Flux suivent des préfixes stricts :
- `p-` (Pages/Layout) : Styles structurels globaux (ex: `.p-page`, `.p-container`, `.p-grid`).
- `c-` (Components) : Composants réutilisables (ex: `.c-btn`, `.c-input`, `.c-card`).
- `u-` (Utilities) : Classes utilitaires simples (ex: `.u-flex`, `.u-mb-0`, `.u-text-center`).

**Règle d'or :** N'utilisez **jamais** de styles en ligne (`style="..."`).

## 2. Le Mode Sombre (Dark Mode)

Flux gère dynamiquement le basculement Clair/Sombre via un système de variables CSS défini dans `resources/css/variables.css`.

Le mode sombre est activé lorsque l'attribut `data-theme="dark"` est présent sur la balise `<html>`.

### Les Variables Sémantiques

Pour que votre plugin supporte instantanément le Dark Mode, utilisez **exclusivement** les variables CSS du Core pour vos couleurs :

- **Surfaces & Fonds** : `var(--surface-white)`, `var(--gray-50)`, `var(--gray-900)`
- **Bordures** : `var(--gray-200)`, `var(--gray-300)`
- **Textes** : `var(--gray-500)` (secondaire), `var(--gray-900)` (principal)
- **Couleurs sémantiques** :
  - Succès : `var(--success-bg)`, `var(--success-border)`, `var(--success-text)`
  - Erreur : `var(--error-bg)`, `var(--error-border)`, `var(--error-text)`
  - Warning : `var(--warning-bg)`, `var(--warning-border)`, `var(--warning-text)`
  - Info : `var(--info-bg)`, `var(--info-border)`, `var(--info-text)`

*Exemple de CSS de plugin :*
```css
/* plugins/MonPlugin/resources/assets/style.css */
.c-mon-plugin-box {
    background-color: var(--surface-white);
    border: 1px solid var(--gray-200);
    color: var(--gray-900);
}
.c-mon-plugin-box:hover {
    background-color: var(--gray-50);
}
```

## 3. Composants Blade du Core

Plutôt que de recréer de l'UI, utilisez les composants Blade fournis par le Core. Ils gèrent automatiquement l'accessibilité et le Dark Mode.

- **Boutons** : `<x-button variant="primary">` (variantes: primary, secondary, danger, ghost)
- **Champs de formulaire** : `<x-input>`, `<x-select>` (ils incluent automatiquement les labels et les styles d'erreur)
- **Cartes** : `<x-card titre="Mon Titre">`
- **Alertes** : `<x-alert type="info">` (types: success, erreur, warning, info)
- **Icônes** : `<x-custom-icon name="chart-bar" class="c-icon--md" />` (utilise Heroicons)

## 4. Ajouter du CSS personnalisé à votre Plugin

Si les composants du Core ne suffisent pas, vous pouvez ajouter vos propres styles :

1. Créez un fichier `resources/assets/style.css` dans le dossier de votre plugin.
2. Écrivez votre CSS en respectant la convention BEM.
3. Flux copiera automatiquement ce fichier dans le dossier `public/` lors de l'activation de votre plugin, et l'injectera dans le `<head>` de toutes les pages d'administration.

## 5. Interactivité (Alpine.js)

Flux n'utilise ni React, ni Vue, ni jQuery. Toute l'interactivité frontend est gérée par **Alpine.js**.

- Alpine est déjà chargé globalement sur la page (`window.Alpine`).
- Utilisez les directives standard (`x-data`, `x-show`, `x-on:click`, etc.) directement dans vos vues Blade.
- Pour les composants complexes, extrayez la logique dans un tag `<script>` en bas de votre vue Blade.

```blade
<div x-data="{ ouvert: false }">
    <x-button @click="ouvert = !ouvert">Basculer</x-button>
    
    <div x-show="ouvert" x-cloak class="p-mt-3">
        Contenu masqué...
    </div>
</div>
```

> [!NOTE]
> La balise `x-cloak` est gérée nativement par le Core pour éviter les flashs de contenu non stylisé (FOUC).
