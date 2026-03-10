<aside :class="`bg-white h-screen fixed left-0 top-0 z-50 transition-all duration-500 shadow-2xl border-r border-gray-100 flex flex-col ${sidebarOpen ? 'w-[280px]' : 'w-[100px]'}`">
    <div :class="`flex flex-col items-center transition-all duration-500 ${sidebarOpen ? 'p-10' : 'p-6'}`">
        <div class="bg-primary p-3 rounded-2xl mb-4 shadow-xl hover:rotate-12 transition-transform duration-500 flex items-center justify-center">
            <i data-lucide="plus" :style="`width: ${sidebarOpen ? '40px' : '28px'}; height: ${sidebarOpen ? '40px' : '28px'}`" class="text-highlight"></i>
        </div>
        <span x-show="sidebarOpen" class="font-black text-primary text-[11px] tracking-[0.4em] uppercase whitespace-nowrap">MedCore HR1</span>
    </div>
    <nav :class="`flex-1 mt-6 overflow-y-auto transition-all duration-500 ${sidebarOpen ? 'px-4' : 'px-2'}`">
        <template x-for="item in navItems" :key="item.id">
            <button @click="activeTab = item.id" :class="`w-full flex items-center transition-all duration-300 rounded-2xl group mb-2 ${activeTab === item.id ? 'bg-primary text-white shadow-xl' : 'text-primary hover:bg-bg hover:scale-[1.02]'} ${sidebarOpen ? 'gap-4 px-6 py-4' : 'justify-center p-4'}`">
                <i :data-lucide="item.icon" class="w-5 h-5" :class="activeTab === item.id ? 'text-highlight' : 'text-accent group-hover:text-primary'"></i>
                <span x-show="sidebarOpen" class="text-[11px] font-black uppercase tracking-[0.1em]" x-text="item.label"></span>
            </button>
        </template>
    </nav>
    <div :class="`transition-all duration-500 ${sidebarOpen ? 'p-8' : 'p-4'}`">
        <button @click="sidebarOpen = !sidebarOpen" class="w-full p-4 bg-bg rounded-[1.5rem] text-primary hover:bg-gray-200 transition-all flex justify-center items-center shadow-inner">
            <i :data-lucide="sidebarOpen ? 'x' : 'menu'" class="w-5 h-5"></i>
        </button>
    </div>
</aside>

