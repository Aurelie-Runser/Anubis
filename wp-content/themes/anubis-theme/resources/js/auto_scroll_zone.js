document.addEventListener('DOMContentLoaded', function() {
    const zoneScrollable = document.querySelector('.zone_messages') || document.querySelector('.logs');
    // Scroll automatiquement en bas
    if (zoneScrollable) {
        zoneScrollable.scrollTop = zoneScrollable.scrollHeight;
    }
});
