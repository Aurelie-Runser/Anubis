import favicon from '@images/favicon-black.png';
        
function setFavicon() {
    if (!window.__isLoggedIn) return;

    document.querySelectorAll("link[rel*='icon']").forEach(el => {
        el.href = favicon + '?v=' + Date.now();
    });
}

// DOM ready
document.addEventListener("DOMContentLoaded", setFavicon);

// fallback si WP injecte après
setTimeout(setFavicon, 500);
setTimeout(setFavicon, 1500);