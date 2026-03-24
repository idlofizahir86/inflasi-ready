<script setup>
import { ref, computed } from 'vue';

defineProps({
    isOpen: Boolean,
});

defineEmits(['close']);

const selectedRegions = ref([]);
const selectedCommodities = ref([]);
const priceRange = ref([0, 100000]);
const volatilityRange = ref([0, 50]);
const dataQuality = ref('all');
const timeRange = ref(7);

const regions = ['DKI Jakarta', 'Jawa Timur', 'Sumatera Utara', 'Jawa Barat', 'Sulawesi Selatan', 'Bali'];
const commodities = ['Cabai Merah', 'Beras Medium', 'Minyak Goreng', 'Bawang Merah', 'Daging Ayam', 'Telur Ayam'];

const resetFilters = () => {
    selectedRegions.value = [];
    selectedCommodities.value = [];
    priceRange.value = [0, 100000];
    volatilityRange.value = [0, 50];
    dataQuality.value = 'all';
    timeRange.value = 7;
};

const applyFilters = () => {
    const filterObj = {
        commodities: selectedCommodities.value,
        regions: selectedRegions.value,
        priceRange: priceRange.value,
        volatilityRange: volatilityRange.value,
        dataQuality: dataQuality.value,
        timeRange: timeRange.value,
    };
    
    this.$emit('apply', filterObj);
};
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <transition name="slide">
                <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-white border-b border-slate-100 px-8 py-6 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Advanced Filtering</p>
                            <h3 class="text-2xl font-headline font-bold text-slate-900">Filter Stream</h3>
                        </div>
                        <button @click="$emit('close')" class="p-2 hover:bg-slate-100 rounded-lg transition">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-8 space-y-8">
                        <!-- Commodities -->
                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-4">Komoditas</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label v-for="c in commodities" :key="c" class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                                    <input v-model="selectedCommodities" :value="c" type="checkbox" class="w-4 h-4">
                                    <span class="text-sm text-slate-700">{{ c }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Regions -->
                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-4">Wilayah</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label v-for="r in regions" :key="r" class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                                    <input v-model="selectedRegions" :value="r" type="checkbox" class="w-4 h-4">
                                    <span class="text-sm text-slate-700">{{ r }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-4">Rentang Harga: Rp {{ priceRange[0].toLocaleString() }} - Rp {{ priceRange[1].toLocaleString() }}</label>
                            <div class="flex gap-4 items-center">
                                <input v-model.number="priceRange[0]" type="range" min="0" max="100000" class="flex-1">
                                <input v-model.number="priceRange[1]" type="range" min="0" max="100000" class="flex-1">
                            </div>
                        </div>

                        <!-- Volatility Range -->
                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-4">Volatilitas: {{ volatilityRange[0] }}% - {{ volatilityRange[1] }}%</label>
                            <div class="flex gap-4 items-center">
                                <input v-model.number="volatilityRange[0]" type="range" min="0" max="50" class="flex-1">
                                <input v-model.number="volatilityRange[1]" type="range" min="0" max="50" class="flex-1">
                            </div>
                        </div>

                        <!-- Data Quality -->
                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-4">Kualitas Data</label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer flex-1">
                                    <input v-model="dataQuality" value="all" type="radio" class="w-4 h-4">
                                    <span class="text-sm text-slate-700">Semua</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer flex-1">
                                    <input v-model="dataQuality" value="verified" type="radio" class="w-4 h-4">
                                    <span class="text-sm text-slate-700">Terverifikasi</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer flex-1">
                                    <input v-model="dataQuality" value="pending" type="radio" class="w-4 h-4">
                                    <span class="text-sm text-slate-700">Pending</span>
                                </label>
                            </div>
                        </div>

                        <!-- Time Range -->
                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-4">Rentang Waktu: {{ timeRange }} hari</label>
                            <select v-model.number="timeRange" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                                <option value="1">1 hari</option>
                                <option value="7">7 hari</option>
                                <option value="30">30 hari</option>
                                <option value="90">90 hari</option>
                            </select>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="sticky bottom-0 bg-white border-t border-slate-100 px-8 py-6 flex gap-3 justify-end">
                        <button @click="resetFilters" class="px-6 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition">
                            Reset
                        </button>
                        <button @click="applyFilters; $emit('close')" class="px-6 py-2.5 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition">
                            Terapkan Filter
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
</style>
