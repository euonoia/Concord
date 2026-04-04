export default function initLandingNavigation() {
    const sections = document.querySelectorAll('section[id]');
    const subLinks = document.querySelectorAll('.sub-link');

    if (sections.length === 0 || subLinks.length === 0) {
        return;
    }

    const updateActiveLink = () => {
        const scrollPos = window.scrollY + 150;

        sections.forEach((section) => {
            const sectionTop = section.offsetTop;
            const sectionBottom = sectionTop + section.offsetHeight;
            const isActive = scrollPos >= sectionTop && scrollPos < sectionBottom;

            if (isActive) {
                subLinks.forEach((link) => {
                    const target = link.getAttribute('href');
                    link.classList.toggle('active', target === `#${section.id}`);
                });
            }
        });
    };

    window.addEventListener('scroll', updateActiveLink);
    updateActiveLink();
}
