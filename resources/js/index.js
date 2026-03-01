// ---------- LOAD GLOBAL COMPONENTS ----------
const components = import.meta.glob('./components/**/*.js')

// ---------- LOAD PAGE MODULES (LAZY) ----------
const pages = import.meta.glob('./pages/**/*.js')


document.addEventListener('DOMContentLoaded', async () => {

    // 1️⃣ Initialize global components
    for (const path in components) {
        const module = await components[path]()
        if (module.default && typeof module.default === 'function') {
            module.default()
        }
    }

    // 2️⃣ Initialize page-specific script
    const page = document.body.dataset.page
    if (!page) return

    const pagePath = `./pages/${page}.js`

    if (pages[pagePath]) {
        const module = await pages[pagePath]()
        if (module.default && typeof module.default === 'function') {
            module.default()
        }
    }
})