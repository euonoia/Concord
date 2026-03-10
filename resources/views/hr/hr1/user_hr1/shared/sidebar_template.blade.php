<!-- Mobile Topbar -->
<div class="topbar">
    <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('show')">
        ☰
    </button>
    <div class="title">MedCore HR1</div>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar" 
     x-data="{ 
         collapsed: window.innerWidth > 768,
         init() {
             // Desktop hover collapse
             const sidebar = this.$el;
             sidebar.addEventListener('mouseenter', () => {
                 if (window.innerWidth > 768 && this.collapsed) {
                     this.collapsed = false;
                 }
             });
             sidebar.addEventListener('mouseleave', () => {
                 if (window.innerWidth > 768) {
                     this.collapsed = true;
                 }
             });
             // Default collapsed on desktop
             if (window.innerWidth > 768) {
                 this.collapsed = true;
             }
             // Auto-close on mobile
             document.addEventListener('click', (e) => {
                 const toggle = document.querySelector('.menu-toggle');
                 if (!sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
                     sidebar.classList.remove('show');
                 }
             });
         }
     }"
     :class="{ 'collapsed': collapsed }">
    <div class="logo">
        <img src="{{ asset('images/hr1/logo.png') }}" alt="HR1 Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div style="display:none; width: 60px; height: 60px; background: var(--accent); border-radius: 10px; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px;">HR1</div>
        <div class="logo-text">MedCore HR1</div>
    </div>

    <nav>
        <template x-for="item in navItems" :key="item.id">
            <a href="#" 
               @click.prevent="activeTab = item.id" 
               :class="{ 'active': activeTab === item.id }"
               class="nav-link">
                <i :class="getIconClass(item.icon)"></i>
                <span x-text="item.label"></span>
                <span class="tooltip" x-text="item.label"></span>
            </a>
        </template>
    </nav>
</div>

<script>
// Make getIconClass available globally for Alpine.js
window.getIconClass = function(iconName) {
    const iconMap = {
        'layout-dashboard': 'bi bi-house-door',
        'users': 'bi bi-people',
        'briefcase': 'bi bi-briefcase',
        'user-plus': 'bi bi-person-plus',
        'trending-up': 'bi bi-graph-up',
        'award': 'bi bi-trophy',
        'user-circle': 'bi bi-person-circle',
        'clipboard-list': 'bi bi-clipboard-check',
        'check-square': 'bi bi-check-square',
        'target': 'bi bi-bullseye',
        'star': 'bi bi-star'
    };
    return iconMap[iconName] || 'bi bi-circle';
};
</script>
