<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    isOpen: Boolean,
    searchQuery: String,
});

defineEmits(['close', 'select']);

const selectedCommodity = ref(null);

// Sample commodity database - dalam praktik akan dari API backend
const commodities = [
    {
        id: 1,
        name: 'Beras Medium',
        category: 'Pangan Pokok',
        price: 14500,
        volatility: 2.3,
        trend: 'up',
        regions: 6,
        lastUpdate: '2024-12-19 14:35',
        description: 'Beras premium kualitas menengah',
        chart: [14200, 14350, 14400, 14500],
    },
    {
        id: 2,
        name: 'Cabai Merah',
        category: 'Sayuran',
        price: 42000,
        volatility: 15.7,
        trend: 'down',
        regions: 6,
        lastUpdate: '2024-12-19 14:30',
        description: 'Cabai merah segar dari berbagai region',
        chart: [45000, 44000, 43000, 42000],
    },
    {
        id: 3,
        name: 'Minyak Goreng',
        category: 'Minyak & Lemak',
        price: 13200,
        volatility: 1.2,
        trend: 'stable',
        regions: 6,
        lastUpdate: '2024-12-19 14:35',
        description: 'Minyak goreng kemasan premium',
        chart: [13150, 13180, 13200, 13200],
    },
    {
        id: 4,
        name: 'Bawang Merah',
        category: 'Rempah',
        price: 28000,
        volatility: 8.5,
        trend: 'up',
        regions: 6,
        lastUpdate: '2024-12-19 14:25',
        description: 'Bawang merah pilihan berkualitas tinggi',
        chart: [26000, 27000, 27500, 28000],
    },
    {
        id: 5,
        name: 'Daging Ayam',
        category: 'Protein',
        price: 38500,
        volatility: 3.1,
        trend: 'up',
        regions: 5,
        lastUpdate: '2024-12-19 14:35',
        description: 'Daging ayam segar tanpa kulit',
        chart: [37800, 38000, 38200, 38500],
    },
    {
        id: 6,
        name: 'Telur Ayam',
        category: 'Protein',
        price: 22000,
        volatility: 5.4,
        trend: 'down',
        regions: 6,
        lastUpdate: '2024-12-19 14:30',
        description: 'Telur ayam segar grade A',
        chart: [23000, 22500, 22200, 22000],
    },
];

const filteredCommodities = computed(() => {
    if (!props.searchQuery) return commodities;
    const query = props.searchQuery.toLowerCase();
    return commodities.filter(c => 
        c.name.toLowerCase().includes(query) ||
        c.category.toLowerCase().includes(query) ||
        c.description.toLowerCase().includes(query)
    );
});

const getTrendIcon = (trend) => {
    return trend === 'up' ? 'trending_up' : trend === 'down' ? 'trending_down' : 'trending_flat';
};

const getTrendColor = (trend) => {
    return trend === 'up' ? 'text-red-600' : trend === 'down' ? 'text-green-600' : 'text-blue-600';
};

const getVolatilityColor = (volatility) => {
    if (volatility > 10) return 'bg-red-100 text-red-700';
    if (volatility > 5) return 'bg-yellow-100 text-yellow-700';
    return 'bg-green-100 text-green-700';
};
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <transition name="slide">
                <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[85vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-6 flex justify-between items-center rounded-t-2xl">
                        <div>
                            <p class="text-xs font-bold text-emerald-100 uppercase tracking-widest">Search Results</p>
                            <h3 class="text-2xl font-headline font-bold text-white">
                                {{ filteredCommodities.length }} Commodity
                                <span v-if="searchQuery">: "{{ searchQuery }}"</span>
                            </h3>
                        </div>
                        <button @click="$emit('close')" class="p-2 hover:bg-emerald-500/30 rounded-lg transition text-white">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- No Results -->
                    <div v-if="filteredCommodities.length === 0" class="p-12 text-center">
                        <span class="material-symbols-outlined text-6xl text-slate-300 block mb-4">search_off</span>
                        <h4 class="font-bold text-slate-700 mb-2">No commodities found</h4>
                        <p class="text-slate-500">Try searching with different keywords</p>
                    </div>

                    <!-- Results Grid -->
                    <div v-else class="p-8 space-y-4">
                        <div v-for="commodity in filteredCommodities" 
                             :key="commodity.id"
                             @click="selectedCommodity = selectedCommodity?.id === commodity.id ? null : commodity"
                             class="bg-white border-2 border-slate-200 rounded-lg p-6 cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/20 transition-all">
                            
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="font-headline font-bold text-lg text-slate-900">{{ commodity.name }}</h4>
                                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                            {{ commodity.category }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600">{{ commodity.description }}</p>
                                </div>
                                <span class="material-symbols-outlined text-slate-400 transition-transform" :class="{ 'rotate-180': selectedCommodity?.id === commodity.id }">expand_more</span>
                            </div>

                            <!-- Price & Stats Row -->
                            <div class="grid grid-cols-4 gap-4 mb-4">
                                <div class="bg-emerald-50 p-3 rounded-lg">
                                    <p class="text-xs text-slate-500 font-bold mb-1">PRICE</p>
                                    <p class="text-xl font-headline font-bold text-emerald-700">
                                        Rp {{ new Intl.NumberFormat('id-ID').format(commodity.price) }}
                                    </p>
                                </div>

                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <p class="text-xs text-slate-500 font-bold mb-1">VOLATILITY</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-xl font-headline font-bold text-blue-700">{{ commodity.volatility }}%</p>
                                        <span :class="[getVolatilityColor(commodity.volatility), 'px-2 py-1 rounded text-xs font-bold']">
                                            {{ commodity.volatility > 10 ? 'High' : commodity.volatility > 5 ? 'Medium' : 'Low' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-3 rounded-lg">
                                    <p class="text-xs text-slate-500 font-bold mb-1">TREND</p>
                                    <div class="flex items-center gap-2">
                                        <span :class="[getTrendColor(commodity.trend), 'material-symbols-outlined text-xl']">
                                            {{ getTrendIcon(commodity.trend) }}
                                        </span>
                                        <p class="font-bold capitalize text-slate-700">{{ commodity.trend }}</p>
                                    </div>
                                </div>

                                <div class="bg-purple-50 p-3 rounded-lg">
                                    <p class="text-xs text-slate-500 font-bold mb-1">REGIONS</p>
                                    <p class="text-xl font-headline font-bold text-purple-700">{{ commodity.regions }}/6</p>
                                </div>
                            </div>

                            <!-- Details Row -->
                            <div class="flex justify-between items-center text-xs text-slate-500 mb-2">
                                <span>Last updated: {{ commodity.lastUpdate }}</span>
                            </div>

                            <!-- Expand Details -->
                            <transition name="expand">
                                <div v-if="selectedCommodity?.id === commodity.id" class="mt-6 pt-6 border-t border-slate-200 space-y-4">
                                    <!-- Price Trend Chart -->
                                    <div>
                                        <h5 class="font-bold text-slate-700 mb-3">7-Day Price Trend</h5>
                                        <div class="flex items-end gap-2 h-32 bg-slate-50 p-4 rounded-lg">
                                            <div v-for="(price, idx) in commodity.chart" :key="idx" class="flex-1 flex flex-col items-center gap-1">
                                                <div class="w-full bg-gradient-to-t from-emerald-500 to-emerald-300 rounded-t-md transition-all hover:opacity-80"
                                                     :style="{ height: (price / Math.max(...commodity.chart)) * 100 + '%', minHeight: '4px' }">
                                                </div>
                                                <span class="text-[10px] text-slate-500 font-bold">{{ Math.floor(price / 1000) }}k</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Regional Distribution -->
                                    <div>
                                        <h5 class="font-bold text-slate-700 mb-3">Available in Regions</h5>
                                        <div class="grid grid-cols-3 gap-2">
                                            <div class="bg-emerald-50 p-3 rounded-lg text-center">
                                                <p class="text-xs font-bold text-slate-500 mb-1">Jakarta</p>
                                                <p class="font-bold text-emerald-700">Rp 14,500</p>
                                            </div>
                                            <div class="bg-emerald-50 p-3 rounded-lg text-center">
                                                <p class="text-xs font-bold text-slate-500 mb-1">Jawa Barat</p>
                                                <p class="font-bold text-emerald-700">Rp 14,200</p>
                                            </div>
                                            <div class="bg-emerald-50 p-3 rounded-lg text-center">
                                                <p class="text-xs font-bold text-slate-500 mb-1">Jawa Timur</p>
                                                <p class="font-bold text-emerald-700">Rp 14,700</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-3 pt-4">
                                        <button @click="$emit('select', commodity)" class="flex-1 bg-emerald-600 text-white py-2 rounded-lg font-bold hover:bg-emerald-700 transition">
                                            View Details
                                        </button>
                                        <button class="flex-1 bg-slate-200 text-slate-700 py-2 rounded-lg font-bold hover:bg-slate-300 transition">
                                            Add to Watchlist
                                        </button>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="sticky bottom-0 px-8 py-6 bg-slate-50 border-t border-slate-100 flex gap-3 justify-end rounded-b-2xl">
                        <button @click="$emit('close')" class="px-6 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-100 transition">
                            Close
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: transform 0.3s; }
.slide-enter-from { transform: translateY(-20px); }
.slide-leave-to { transform: translateY(-20px); }

.expand-enter-active, .expand-leave-active { transition: all 0.3s ease; }
.expand-enter-from { 
    opacity: 0;
    max-height: 0;
}
.expand-leave-to { 
    opacity: 0;
    max-height: 0;
}
</style>
