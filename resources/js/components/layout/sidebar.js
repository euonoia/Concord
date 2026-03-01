export default function initSidebar() {

    const sidebar = document.getElementById('sidebar')
    const toggle = document.querySelector('.menu-toggle')

    if (!sidebar) return

    if (window.innerWidth > 768) {
        sidebar.classList.add('collapsed')
    }

    sidebar.addEventListener('mouseenter', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('collapsed')
        }
    })

    sidebar.addEventListener('mouseleave', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.add('collapsed')
        }
    })

    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !toggle?.contains(e.target)) {
            sidebar.classList.remove('show')
        }
    })
}