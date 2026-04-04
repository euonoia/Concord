// Global component & page loader
const components = import.meta.glob('./components/**/*.js');
const pages = import.meta.glob('./pages/**/*.js');

let alpineStarted = false;

const startAlpine = () => {
    if (!window.Alpine || alpineStarted) return;
    window.Alpine.start();
    alpineStarted = true;
};

document.addEventListener('DOMContentLoaded', async () => {
    // Load components
    for (const path in components) {
        const mod = await components[path]();
        mod.default?.();
    }

    // Load page-specific module
    const page = document.body.dataset.page || document.querySelector('[data-page]')?.dataset.page;
    if (page) {
        const modulePath = `./pages/${page}.js`;
        if (pages[modulePath]) {
            const mod = await pages[modulePath]();
            mod.default?.();
        }
    }

    startAlpine();
});