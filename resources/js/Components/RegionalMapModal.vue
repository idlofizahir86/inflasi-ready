<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    regions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const selectedRegion = ref(null);

const regionDetails = computed(() => {
    const detailsMap = {
        'DKI Jakarta': {
            status: 'CRITICAL',
            inflation: 4.2,
            topCommodity: 'Cabai Merah',
            topPrice: '52.400',
            volatility: '18.2%',
            markets: 45,
        },
        'Jawa Timur': {
            status: 'MODERATE',
            inflation: 3.1,
            topCommodity: 'Daging Ayam',
            topPrice: '38.900',
            volatility: '9.4%',
            markets: 78,
        },
        'Sumatera Utara': {
            status: 'STABLE',
            inflation: 2.3,
            topCommodity: 'Beras Medium',
            topPrice: '14.200',
            volatility: '3.2%',
            markets: 52,
        },
    };
    return detailsMap[selectedRegion.value?.name] || null;
});

const closeModal = () => {
    selectedRegion.value = null;
    emit('close');
};

const getStatusColor = (status) => {
    switch(status) {
        case 'CRITICAL': return 'bg-error/10 text-error';
        case 'MODERATE': return 'bg-orange-100 text-orange-700';
        case 'STABLE': return 'bg-primary/10 text-primary';
        default: return 'bg-slate-100 text-slate-700';
    }
};

const getStatusBgColor = (status) => {
    switch(status) {
        case 'CRITICAL': return 'bg-error';
        case 'MODERATE': return 'bg-orange-400';
        case 'STABLE': return 'bg-primary';
        default: return 'bg-slate-400';
    }
};
</script>

<template>
    <!-- Modal Overlay -->
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click="closeModal">
            <transition name="slide">
                <div v-if="isOpen" class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
                    <!-- Header -->
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                        <div>
                            <h2 class="text-2xl font-bold font-headline text-on-surface">Regional Heatmap</h2>
                            <p class="text-sm text-on-surface-variant">Click a region to see detailed metrics</p>
                        </div>
                        <button @click="closeModal" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto">
                        <div class="p-8">
                            <!-- Region Grid -->
                            <div v-if="!selectedRegion" class="space-y-6">
                                <h3 class="text-lg font-bold font-headline text-on-surface mb-4">Price Status by Province</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <button 
                                        v-for="region in regions" 
                                        :key="region.name"
                                        @click="selectedRegion = region"
                                        class="p-6 rounded-lg border-2 border-slate-200 hover:border-primary hover:bg-primary/5 transition-all text-left group">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div :class="['w-4 h-4 rounded-full', region.color, 'group-hover:scale-125 transition-transform']"></div>
                                            <h4 class="text-lg font-bold font-headline">{{ region.name }}</h4>
                                        </div>
                                        <div :class="['inline-block px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest', getStatusColor(region.status)]">
                                            {{ region.status }}
                                        </div>
                                        <div class="mt-4 text-xs text-on-surface-variant flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                            View details
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Region Details -->
                            <div v-else class="space-y-6">
                                <button @click="selectedRegion = null" class="flex items-center gap-2 text-primary hover:text-primary-dark transition-colors mb-4">
                                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                                    <span class="text-sm font-bold">Back to Map</span>
                                </button>

                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-3xl font-bold font-headline text-on-surface mb-2">{{ selectedRegion.name }}</h3>
                                        <div :class="['inline-block px-4 py-2 rounded-full text-sm font-bold uppercase tracking-widest', getStatusColor(regionDetails.status)]">
                                            {{ regionDetails.status }}
                                        </div>
                                    </div>
                                    <div :class="['h-16 w-16 rounded-full flex items-center justify-center text-white', getStatusBgColor(regionDetails.status)]">
                                        <span class="material-symbols-outlined text-3xl">location_on</span>
                                    </div>
                                </div>

                                <!-- Stats Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                        <p class="text-xs text-on-surface-variant font-medium mb-1">MoM Inflation</p>
                                        <p class="text-2xl font-bold font-headline text-on-surface">{{ regionDetails.inflation }}%</p>
                                    </div>
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                        <p class="text-xs text-on-surface-variant font-medium mb-1">Volatility</p>
                                        <p class="text-2xl font-bold font-headline text-error">{{ regionDetails.volatility }}</p>
                                    </div>
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                        <p class="text-xs text-on-surface-variant font-medium mb-1">Active Markets</p>
                                        <p class="text-2xl font-bold font-headline text-on-surface">{{ regionDetails.markets }}</p>
                                    </div>
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                        <p class="text-xs text-on-surface-variant font-medium mb-1">Last Updated</p>
                                        <p class="text-sm font-bold text-primary mt-1">5 mins ago</p>
                                    </div>
                                </div>

                                <!-- Top Commodity -->
                                <div class="bg-gradient-to-br from-primary/5 to-secondary/5 p-6 rounded-lg border border-primary/20">
                                    <h4 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">Trending Commodity</h4>
                                    <div class="flex items-baseline gap-2 mb-2">
                                        <span class="text-2xl font-bold font-headline">{{ regionDetails.topCommodity }}</span>
                                        <span class="text-primary text-sm font-bold">Highest Alert</span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-on-surface-variant text-sm">Rp</span>
                                        <span class="text-3xl font-bold font-headline text-on-surface">{{ regionDetails.topPrice }}</span>
                                        <span class="text-on-surface-variant text-xs ml-2">/ kg</span>
                                    </div>
                                </div>

                                <!-- Market Data Table -->
                                <div>
                                    <h4 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">Market Data Feed</h4>
                                    <div class="space-y-2">
                                        <div v-for="n in 5" :key="n" class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                                            <div class="flex items-center gap-3 flex-1">
                                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                                <span class="text-sm font-medium text-on-surface">Pasar {{ String.fromCharCode(65 + n - 1) }}</span>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <span class="text-xs text-on-surface-variant">{{ Math.floor(Math.random() * 5) + 1 }}h ago</span>
                                                <span class="text-sm font-bold text-primary">Rp {{ (10000 + Math.random() * 50000).toFixed(0) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between sticky bottom-0">
                        <p class="text-xs text-on-surface-variant">Data updated every 30 minutes | <span class="text-primary font-semibold">Blockchain Verified</span></p>
                        <button @click="closeModal" class="px-6 py-2 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary-dark transition-colors">
                            Close Map
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

.slide-enter-active, .slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from {
    transform: translateY(-20px);
}

.slide-leave-to {
    transform: translateY(-20px);
}
</style>
