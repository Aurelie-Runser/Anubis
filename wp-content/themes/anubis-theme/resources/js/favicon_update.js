import favicon_black from '@images/favicon-black.png';
import favicon_is from '@images/favicon-is.png';

function setFavicon(icon, title) {
    document.querySelectorAll("link[rel*='icon']").forEach(el => {
        el.href = icon + '?v=' + Date.now();
    });

    document.title = title;
}

// état WP par défaut (non connecté)
const isLoggedIn = window.__isLoggedIn;

// favicon custom uniquement si connecté
const defaultFavicon = isLoggedIn ? favicon_black : null;
const awayFavicon = favicon_is;

const defaultTitle = document.title;
const awayTitle = "...";

// initial
if (isLoggedIn) {
    setFavicon(defaultFavicon, defaultTitle);
}

// détection changement onglet
document.addEventListener("visibilitychange", () => {

    if (!isLoggedIn) return; // ⛔ ne rien faire si non connecté

    if (document.visibilityState === "hidden") {
        setTimeout(() => {
            setFavicon(awayFavicon, awayTitle);
        }, 50);
    }

    if (document.visibilityState === "visible") {
        setTimeout(() => {
            setFavicon(defaultFavicon, defaultTitle);
        }, 50);
    }
});