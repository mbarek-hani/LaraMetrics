(function () {
    "use strict";

    // ─── Configuration ────────────────────────────────────────────
    const script = document.currentScript;
    const token = script?.getAttribute("data-token");
    const endpoint =
        script?.getAttribute("data-endpoint") ||
        script?.src.replace("tracker.js", "api/track");

    // Abandon si pas de token
    if (!token) {
        console.warn("[Flux] data-token manquant sur le script.");
        return;
    }

    // ─── Respect Do Not Track ─────────────────────────────────────
    if (navigator.doNotTrack === "1" || window.doNotTrack === "1") {
        console.warn("[Flux] stop tracking due to navigator.doNotTrack");
        return;
    }

    // ─── Ne pas tracker les bots ──────────────────────────────────
    const botsPattern = /bot|crawl|spider|slurp|teoma|archive|track/i;
    if (botsPattern.test(navigator.userAgent)) {
        return;
    }

    // ─── Ne pas tracker en localhost (dev) ────────────────────────
    // const hote = window.location.hostname;
    // if (hote === 'localhost' || hote === '127.0.0.1' || hote === '') {
    //     return;
    // }

    // ─── Détection de l'appareil ──────────────────────────────────
    function detecterAppareil() {
        const ua = navigator.userAgent;
        if (/tablet|ipad|playbook|silk/i.test(ua)) return "tablette";
        if (
            /mobile|iphone|ipod|android|blackberry|mini|windows\sce|palm/i.test(
                ua,
            )
        )
            return "mobile";
        return "ordinateur";
    }

    // ─── Extraction du domaine référent ───────────────────────────
    function extraireDomaineReferent(referentUrl) {
        if (!referentUrl) return null;
        try {
            return new URL(referentUrl).hostname;
        } catch {
            return null;
        }
    }

    // ─── Extraction des paramètres UTM ────────────────────────────
    function extraireUtm() {
        const params = new URLSearchParams(window.location.search);
        return {
            utm_source: params.get("utm_source") || null,
            utm_medium: params.get("utm_medium") || null,
            utm_campagne: params.get("utm_campaign") || null,
        };
    }

    // ─── Durée de session ─────────────────────────────────────────
    let tempsDebut = Date.now();

    // ─── Envoi des données ────────────────────────────────────────
    function envoyer(donnees) {
        // Utilise sendBeacon si disponible (plus fiable au unload)
        const payload = JSON.stringify(donnees);

        console.info("[Flux]: envoyons les données");
        console.log(payload);

        try {
            if (navigator.sendBeacon) {
                const blob = new Blob([payload], { type: "application/json" });
                navigator.sendBeacon(endpoint, blob);
            } else {
                // Fallback fetch synchrone
                fetch(endpoint, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: payload,
                    keepalive: true,
                });
            }
        } catch (err) {
            console.error(
                `[Flux] there was an unexpected error sending ${payload}`,
                err,
            );
        }
    }

    // ─── Collecte principale ──────────────────────────────────────
    function tracker(estNavigation) {
        const utm = extraireUtm();

        const donnees = {
            token: token,
            url: window.location.href,
            chemin: window.location.pathname,
            titre: document.title || null,
            referent: estNavigation ? null : document.referrer || null,
            referent_domaine: estNavigation
                ? null
                : extraireDomaineReferent(document.referrer),
            appareil: detecterAppareil(),
            largeur_ecran: window.innerWidth,
            est_navigation: estNavigation || false,
            ...utm,
        };

        envoyer(donnees);
    }

    // ─── Envoi de la durée au départ de la page ───────────────────
    function envoyerDuree() {
        const duree = Math.round((Date.now() - tempsDebut) / 1000);

        // On envoie la durée seulement si > 2 secondes (évite les bounces accidentels)
        if (duree < 2) return;

        envoyer({
            token: token,
            type: "duree",
            chemin: window.location.pathname,
            duree: duree,
        });
    }

    // ─── Initialisation ───────────────────────────────────────────

    // Tracker la page actuelle
    tracker(false);

    // Envoyer la durée quand l'utilisateur quitte
    window.addEventListener("visibilitychange", function () {
        if (document.visibilityState === "hidden") {
            envoyerDuree();
        }
    });

    window.addEventListener("beforeunload", envoyerDuree);

    // API publique pour tracker des événements personnalisés
    // Utilisation : Flux.event('clic_bouton', { bouton: 'inscription' })
    window.Flux = {
        event: function (nom, donnees) {
            envoyer({
                token: token,
                type: "evenement",
                nom: nom,
                chemin: window.location.pathname,
                donnees: donnees || {},
            });
        },
    };

    // ─── Plugin injection point ───────────────────────────────
    /* PLUGIN_INJECTION */
})();
