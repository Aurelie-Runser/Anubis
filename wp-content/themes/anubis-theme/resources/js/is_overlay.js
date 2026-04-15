function randomRange(min, max) {
    return Math.random() * (max - min) + min;
}

function showIs() {
    const is = document.getElementById("is-overlay");

    // durée d'apparition variable
    const visibleTime = randomRange(500, 6000);

    is.style.opacity = "0.2";

    setTimeout(() => {
        is.style.opacity = "0";
    }, visibleTime);
}

function scheduleIs() {

    // délai ULTRA irrégulier
    let delay;

    const r = Math.random();

    if (r < 0.6) {
        // apparition fréquente
        delay = randomRange(6000, 30000);
    } 
    else if (r < 0.9) {
        // plus rare
        delay = randomRange(10000, 90000);
    } 
    else {
        // très rare (pause longue)
        delay = randomRange(70000, 240000);
    }

    setTimeout(() => {
        showIs();
        scheduleIs();
    }, delay);
}

scheduleIs();
