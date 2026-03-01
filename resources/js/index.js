// Global component & page loader
const components = import.meta.glob('./components/**/*.js');
const pages = import.meta.glob('./pages/**/*.js');

document.addEventListener('DOMContentLoaded', async () => {
    // Load components
    for (const path in components) {
        const mod = await components[path]();
        mod.default?.();
    }

    // Load page-specific module
    const page = document.body.dataset.page;
    if (!page) return;

    const modulePath = `./pages/${page}.js`;
    if (pages[modulePath]) {
        const mod = await pages[modulePath]();
        mod.default?.();
    }
});