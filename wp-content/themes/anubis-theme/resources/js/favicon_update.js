import favicon_black from '@images/favicon-black.png';
import favicon_is from '@images/favicon-is.png';

function setFavicon(icon, title) {
    document.querySelectorAll("link[rel*='icon']").forEach(el => {
        el.href = icon + '?v=' + Date.now();
    });
    
    document.title = title;
}

// état normal
const defaultFavicon = favicon_black;
const awayFavicon = favicon_is;

const defaultTitle = document.title;
const awayTitle = "...";

// initial
setFavicon(defaultFavicon, defaultTitle);

// détection changement onglet
document.addEventListener("visibilitychange", () => {

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