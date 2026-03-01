export default function initSidebar() {

    const sidebar = document.getElementById('sidebar')
    const toggle  = document.querySelector('.menu-toggle')

    if (!sidebar) return

    const breakpoint = 768

    function applyDesktopState() {
        sidebar.classList.add('collapsed')
        sidebar.classList.remove('show')
    }

    function applyMobileState() {
        sidebar.classList.remove('collapsed')
        sidebar.classList.remove('show')
    }

    function handleResize() {
        if (window.innerWidth > breakpoint) {
            applyDesktopState()
        } else {
            applyMobileState()
        }
    }

    // Initial state
    handleResize()

    // Resize listener
    window.addEventListener('resize', handleResize)

    // Toggle button (mobile only)
    toggle?.addEventListener('click', () => {
        if (window.innerWidth <= breakpoint) {
            sidebar.classList.toggle('show')
        }
    })

    // Hover expand (desktop only)
    sidebar.addEventListener('mouseenter', () => {
        if (window.innerWidth > breakpoint) {
            sidebar.classList.remove('collapsed')
        }
    })

    sidebar.addEventListener('mouseleave', () => {
        if (window.innerWidth > breakpoint) {
            sidebar.classList.add('collapsed')
        }
    })

    // Close sidebar when clicking outside (mobile only)
    document.addEventListener('click', (e) => {
        if (
            window.innerWidth <= breakpoint &&
            !sidebar.contains(e.target) &&
            !toggle?.contains(e.target)
        ) {
            sidebar.classList.remove('show')
        }
    })
}