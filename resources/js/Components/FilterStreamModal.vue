<script setup>
import { ref } from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'apply']);

// Filter states
const filters = ref({
    commodities: [],
    regions: [],
    priceRange: [0, 100000],
    volatilityRange: [0, 50],
    dataQuality: 'all', // all, verified, pending
    timeRange: 7, // days
});

const commodityOptions = [
    'Cabai Merah', 'Beras Medium', 'Minyak Goreng',
    'Bawang Merah', 'Daging Ayam', 'Telur Ayam'
];

const regionOptions = [
    'DKI Jakarta', 'Jawa Timur', 'Sumatera Utara',
    'Jawa Barat', 'Sulawesi Selatan', 'Bali'
];

const closeModal = () => {
    emit('close');
};

const applyFilters = () => {
    emit('apply', filters.value);
    closeModal();
};

const resetFilters = () => {
    filters.value = {
        commodities: [],
        regions: [],
        priceRange: [0, 100000],
        volatilityRange: [0, 50],
        dataQuality: 'all',
        timeRange: 7,
    };
};
</script>

<template>
    <!-- Modal Overlay -->
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click="closeModal">
            <transition name="slide">
                <div v-if="isOpen" class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
                    <!-- Header -->
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                        <div>
                            <h2 class="text-2xl font-bold font-headline text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">filter_list</span>
                                Advanced Stream Filter
                            </h2>
                            <p class="text-sm text-on-surface-variant">Refine data stream with advanced criteria</p>
                        </div>
                        <button @click="closeModal" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-8 space-y-6">
                        <!-- Commodities Filter -->
                        <div>
                            <label class="block text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">
                                <span class="material-symbols-outlined text-sm align-middle">agriculture</span>
                                Commodities
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label v-for="commodity in commodityOptions" :key="commodity" class="flex items-center gap-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="filters.commodities" 
                                        :value="commodity"
                                        class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer"
                                    />
                                    <span class="text-sm text-on-surface-variant">{{ commodity }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Regions Filter -->
                        <div>
                            <label class="block text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">
                                <span class="material-symbols-outlined text-sm align-middle">location_on</span>
                                Regions
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label v-for="region in regionOptions" :key="region" class="flex items-center gap-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="filters.regions" 
                                        :value="region"
                                        class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer"
                                    />
                                    <span class="text-sm text-on-surface-variant">{{ region }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">
                                <span class="material-symbols-outlined text-sm align-middle">currency_exchange</span>
                                Price Range (Rp)
                            </label>
                            <div class="space-y-2">
                                <input 
                                    type="range" 
                                    v-model="filters.priceRange[0]" 
                                    min="0" 
                                    max="100000"
                                    class="w-full"
                                />
                                <div class="flex items-center justify-between text-xs text-on-surface-variant">
                                    <span>Rp {{ filters.priceRange[0].toLocaleString() }}</span>
                                    <span>- Rp {{ filters.priceRange[1].toLocaleString() }}</span>
                                </div>
                                <input 
                                    type="range" 
                                    v-model="filters.priceRange[1]" 
                                    min="0" 
                                    max="100000"
                                    class="w-full"
                                />
                            </div>
                        </div>

                        <!-- Data Quality -->
                        <div>
                            <label class="block text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">
                                <span class="material-symbols-outlined text-sm align-middle">verified</span>
                                Data Quality
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filters.dataQuality" value="all" class="cursor-pointer" />
                                    <span class="text-sm text-on-surface-variant">All Data</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filters.dataQuality" value="verified" class="cursor-pointer" />
                                    <span class="text-sm text-on-surface-variant">Blockchain Verified Only</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="filters.dataQuality" value="pending" class="cursor-pointer" />
                                    <span class="text-sm text-on-surface-variant">Pending Verification</span>
                                </label>
                            </div>
                        </div>

                        <!-- Time Range -->
                        <div>
                            <label class="block text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">
                                <span class="material-symbols-outlined text-sm align-middle">schedule</span>
                                Time Range
                            </label>
                            <select v-model="filters.timeRange" class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm">
                                <option value="1">Last 24 Hours</option>
                                <option value="7">Last 7 Days</option>
                                <option value="30">Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between sticky bottom-0">
                        <button @click="resetFilters" class="px-4 py-2 text-slate-700 font-bold text-sm rounded-lg hover:bg-white transition-colors">
                            Reset Filters
                        </button>
                        <div class="flex gap-3">
                            <button @click="closeModal" class="px-6 py-2 border border-slate-300 text-slate-700 font-bold text-sm rounded-lg hover:bg-slate-50 transition-colors">
                                Cancel
                            </button>
                            <button @click="applyFilters" class="px-6 py-2 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary-dark transition-colors">
                                Apply Filters
                            </button>
                        </div>
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
