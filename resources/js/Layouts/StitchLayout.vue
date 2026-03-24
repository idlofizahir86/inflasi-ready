<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CommoditySearchResults from '@/Components/CommoditySearchResults.vue';

const page = usePage();
const searchQuery = ref('');
const showSearchResults = ref(false);
const searchSuggestions = ref([]);
const showSuggestions = ref(false);

// Konfigurasi Navigasi Dinamis untuk TopBar
const topNavLinks = computed(() => {
    const currentRoute = route().current();
    
    // Jika di halaman API Settings, tampilkan menu developer
    if (currentRoute.startsWith('api-settings')) {
        return [
            { name: 'API Keys', route: 'api-settings', active: currentRoute === 'api-settings' },
            { name: 'Documentation', route: '#', active: false },
            { name: 'Usage Log', route: '#', active: false },
        ];
    }

    // Default: Tampilkan menu utama (Dashboard/Data Center/Simulation)
    return [
        { name: 'Overview', route: 'dashboard', active: currentRoute === 'dashboard' },
        { name: 'Regional', route: 'datacenter', active: currentRoute === 'datacenter' },
        { name: 'Analytics', route: 'simulation', active: currentRoute === 'simulation' },
    ];
});

// Sample commodity list untuk autocomplete - dalam praktik dari API
const commodityList = [
    'Beras Medium', 'Cabai Merah', 'Minyak Goreng', 'Bawang Merah', 'Daging Ayam', 'Telur Ayam',
    'Gula Pasir', 'Garam Beryodium', 'Kedelai', 'Jagung', 'Daging Sapi', 'Ikan Segar'
];

// Real-time search dengan suggestions
const handleSearchInput = () => {
    const query = searchQuery.value.trim();
    
    if (query.length > 0) {
        // Filter suggestions berdasarkan input
        searchSuggestions.value = commodityList.filter(item =>
            item.toLowerCase().includes(query.toLowerCase())
        ).slice(0, 5);
        showSuggestions.value = true;
    } else {
        showSuggestions.value = false;
    }
};

// Handle select dari suggestion
const selectSuggestion = (suggestion) => {
    searchQuery.value = suggestion;
    showSuggestions.value = false;
    performSearch();
};

// Perform actual search
const performSearch = () => {
    const query = searchQuery.value.trim();
    if (query) {
        const currentRoute = route().current();
        
        if (currentRoute === 'api-settings') {
            alert(`Searching API documentation for: "${query}"`);
            console.log('API Search:', query);
        } else {
            // Show commodity search results
            showSearchResults.value = true;
            console.log('Commodity Search:', query);
        }
    }
};

const handleSearch = () => {
    performSearch();
    searchQuery.value = '';
};
</script>

<template>
    <div class="min-h-screen bg-surface text-on-surface">
        <aside class="h-screen w-64 fixed left-0 top-0 border-r-0 bg-slate-50 flex flex-col py-6 z-50">
            <div class="px-6 mb-10">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">
                        agriculture
                    </span>
                    <h1 class="text-xl font-bold tracking-tight text-primary font-headline">
                        Inflasi-Ready
                    </h1>
                </div>
                <p class="text-xs font-medium uppercase tracking-widest text-primary/60">
                    Ketahanan Pangan
                </p>
            </div>

            <nav class="flex-1 space-y-1">
                <Link :href="route('dashboard')" 
                    :class="[route().current('dashboard') ? 'text-emerald-900 font-semibold border-r-4 border-emerald-800 bg-emerald-50/50' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50']"
                    class="flex items-center gap-3 px-4 py-3 transition-all duration-200 active:scale-95">
                    <span class="material-symbols-outlined" :style="route().current('dashboard') ? 'font-variation-settings: \'FILL\' 1;' : ''">dashboard</span>
                    <span class="font-headline font-medium text-sm tracking-tight">Dashboard</span>
                </Link>

                <Link :href="route('datacenter')" 
                    :class="[route().current('datacenter') ? 'text-emerald-900 font-semibold border-r-4 border-emerald-800 bg-emerald-50/50' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50']"
                    class="flex items-center gap-3 px-4 py-3 transition-all duration-200 active:scale-95">
                    <span class="material-symbols-outlined" :style="route().current('datacenter') ? 'font-variation-settings: \'FILL\' 1;' : ''">database</span>
                    <span class="font-headline font-medium text-sm tracking-tight">Data Center</span>
                </Link>

                <Link :href="route('simulation')" 
                    :class="[route().current('simulation') ? 'text-emerald-900 font-semibold border-r-4 border-emerald-800 bg-emerald-50/50' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50']"
                    class="flex items-center gap-3 px-4 py-3 transition-all duration-200 active:scale-95">
                    <span class="material-symbols-outlined" :style="route().current('simulation') ? 'font-variation-settings: \'FILL\' 1;' : ''">analytics</span>
                    <span class="font-headline font-medium text-sm tracking-tight">Simulation</span>
                </Link>

                <Link :href="route('api-settings')" 
                    :class="[route().current('api-settings') ? 'text-emerald-900 font-semibold border-r-4 border-emerald-800 bg-emerald-50/50' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50']"
                    class="flex items-center gap-3 px-4 py-3 transition-all duration-200 active:scale-95">
                    <span class="material-symbols-outlined" :style="route().current('api-settings') ? 'font-variation-settings: \'FILL\' 1;' : ''">code</span>
                    <span class="font-headline font-medium text-sm tracking-tight">API Settings</span>
                </Link>

                <div class="h-px bg-slate-200 my-2"></div>

                <Link :href="route('profile.edit')" 
                    :class="[route().current('profile.edit') ? 'text-emerald-900 font-semibold border-r-4 border-emerald-800 bg-emerald-50/50' : 'text-slate-500 hover:text-emerald-800 hover:bg-emerald-50']"
                    class="flex items-center gap-3 px-4 py-3 transition-all duration-200 active:scale-95">
                    <span class="material-symbols-outlined" :style="route().current('profile.edit') ? 'font-variation-settings: \'FILL\' 1;' : ''">person</span>
                    <span class="font-headline font-medium text-sm tracking-tight">Profile</span>
                </Link>
            </nav>

            <div class="mt-auto px-4 space-y-1">
                <div class="px-4 py-3 mb-4 rounded-lg bg-emerald-900 text-white text-[10px] font-bold text-center tracking-widest uppercase flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
                    Blockchain Verified
                </div>
                
                <Link :href="route('support')" 
                    target="_blank" 
                    class="flex items-center gap-3 px-4 py-2 text-slate-500 hover:text-emerald-800 transition-colors">
                    <span class="material-symbols-outlined">help</span>
                    <span class="font-headline font-medium text-sm tracking-tight">Support</span>
                </Link>
                
                <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-3 px-4 py-2 text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-headline font-medium text-sm tracking-tight">Logout</span>
                </Link>
            </div>
        </aside>

        <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-8 ml-64 shadow-sm">
            <div class="flex items-center gap-8">
                <nav class="flex gap-6 items-center">
                    <Link v-for="link in topNavLinks" 
                        :key="link.name"
                        :href="link.route !== '#' ? route(link.route) : '#'"
                        :class="[
                            link.active 
                            ? 'text-emerald-800 font-bold border-b-2 border-emerald-800 pb-1' 
                            : 'text-slate-500 hover:text-emerald-700'
                        ]"
                        class="font-headline text-sm transition-all duration-200"
                        preserve-scroll>
                        {{ link.name }}
                    </Link>
                </nav>
            </div>

            <div class="flex items-center gap-6">
                <div class="relative hidden lg:block w-72">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                    <input 
                        v-model="searchQuery"
                        @input="handleSearchInput"
                        @keyup.enter="handleSearch"
                        @keydown.esc="showSuggestions = false"
                        class="pl-10 pr-4 py-2 bg-slate-100 border-2 border-transparent rounded-full text-sm focus:border-emerald-500 focus:ring-0 focus:outline-none w-full transition-all" 
                        :placeholder="route().current('api-settings') ? 'Search API docs...' : 'Search commodity, price, trend...'" 
                        type="text"
                        autocomplete="off"/>
                    
                    <!-- Search Suggestions Dropdown -->
                    <transition name="dropdown">
                        <div v-if="showSuggestions && searchSuggestions.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-200 rounded-lg shadow-xl z-50">
                            <div class="max-h-48 overflow-y-auto">
                                <div v-for="(suggestion, idx) in searchSuggestions" 
                                     :key="idx"
                                     @click="selectSuggestion(suggestion)"
                                     class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-slate-100 last:border-b-0 transition">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-400 text-sm">shopping_bag</span>
                                        <span class="font-medium text-slate-700">{{ suggestion }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 px-4 py-2 text-xs text-slate-500 font-bold border-t border-slate-200">
                                PRESS ENTER TO SEARCH
                            </div>
                        </div>
                    </transition>
                </div>
                
                <div class="flex items-center gap-4">
                    <button class="material-symbols-outlined text-slate-500 hover:text-emerald-600">notifications</button>
                    
                    <div class="group relative">
                        <div class="h-8 w-8 rounded-full bg-slate-200 overflow-hidden border border-slate-100 cursor-pointer">
                            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYOMV4JO4-K2NzCrBb7ybin0weQxK5DRU2q1zpd6VodS3-psISjVHY38q7Sg7NWBq16QvC17ksbhAtLm0ENcqqyJy-4kfwHpgqRF0TFt19L2a2jBF-yVw35G7kTXo6NNB2Swv5eP0hwuI_FqLQqMZzEqf2sV7QdAYuvOOxjoCbwN2_6ackZ-awG_sv2F7W_mLpO1AiUacbxAQnOJe2N_rmXsk5SERVghOJdivgz57c69LEL0zr7S6ypqn_m-5k5PsoQkpAATg8ZwU" alt="Admin" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute right-0 mt-2 w-60 bg-white rounded-xl shadow-xl border border-slate-100 py-2 hidden group-hover:block z-50">
                                <div class="border-t border-slate-50 my-1">
                            <span class="block px-4 py-2 text-xs text-slate-400 font-headline">Logged in as akun-demo@pidi.id</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="ml-64 pt-24 pb-16 px-8 max-w-[1600px] mx-auto">
            <slot />
        </main>

        <!-- Commodity Search Results Modal -->
        <CommoditySearchResults 
            :isOpen="showSearchResults" 
            :searchQuery="searchQuery"
            @close="showSearchResults = false"
            @select="(commodity) => { console.log('Selected commodity:', commodity); showSearchResults = false; }" />
    </div>
</template>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active { transition: all 0.2s; }
.dropdown-enter-from { 
    opacity: 0;
    transform: translateY(-8px);
}
.dropdown-leave-to { 
    opacity: 0;
    transform: translateY(-8px);
}
</style>