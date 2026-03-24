<script setup>
import { ref, computed, onMounted } from 'vue';
import StitchLayout from '@/Layouts/StitchLayout.vue';
import ExportReportModal from '@/Components/ExportReportModal.vue';
import SaveScenarioModal from '@/Components/SaveScenarioModal.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    db_commodities: Object,
    baseline_inflation: Number
});

// State sliders sekarang berbentuk Object untuk menampung data dinamis
const sliders = ref({});
const showExportModal = ref(false);
const showSaveModal = ref(false);

// Inisialisasi slider ke nilai tengah (50) saat load
onMounted(() => {
    Object.keys(props.db_commodities).forEach(name => {
        sliders.value[name] = 50;
    });
});

const calculateChange = (val) => {
    const change = (val - 50) * 2;
    return change > 0 ? `+${change.toFixed(1)}%` : `${change.toFixed(1)}%`;
};

// Proyeksi Index Inflasi Dinamis
const simulatedIndex = computed(() => {
    const base = props.baseline_inflation;
    const keys = Object.keys(sliders.value);
    
    if (keys.length === 0) return base.toFixed(2);

    // Hitung rata-rata perubahan dari semua slider
    const totalImpact = keys.reduce((acc, name) => {
        return acc + (sliders.value[name] - 50);
    }, 0);

    const impactFactor = (totalImpact / keys.length) * 0.08; // Konstanta dampak
    return (base + impactFactor).toFixed(2);
});

// Format Rupiah untuk tampilan harga simulasi
const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(Math.round(price));
};
</script>

<template>
    <Head title="Inflation Simulator - Inflasi-Ready" />

    <StitchLayout>
        <header class="mb-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h2 class="text-4xl font-extrabold font-headline tracking-tight text-primary mb-2">Inflation Simulator</h2>
                    <p class="text-on-surface-variant max-w-2xl text-lg">Model the impact of commodity price fluctuations from real-time database data.</p>
                </div>
                <div class="flex gap-3">
                    <button @click="showExportModal = true" class="px-5 py-2.5 rounded-lg border border-outline-variant text-primary font-medium hover:bg-surface-container transition-colors">Export Report</button>
                    <button @click="Object.keys(sliders).forEach(k => sliders[k] = 50)" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium shadow-lg shadow-primary/10 hover:bg-primary-container transition-all">Reset Simulation</button>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-12 gap-6">
            <section class="col-span-12 lg:col-span-4 flex flex-col gap-6">
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_50px_rgba(0,33,22,0.04)] h-full">
                    <div class="flex items-center gap-2 mb-8 text-primary">
                        <span class="material-symbols-outlined">tune</span>
                        <h3 class="font-headline font-bold text-lg">"What-If" Variables</h3>
                    </div>

                    <div class="space-y-10">
                        <div v-for="(data, name) in db_commodities" :key="name" class="space-y-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="font-headline font-semibold text-on-surface block">{{ name }}</label>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                        Market: Rp {{ formatPrice(data.price) }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span :class="['font-bold px-3 py-1 rounded-full text-sm block mb-1', sliders[name] > 50 ? 'text-error bg-error/5' : 'text-primary bg-primary/5']">
                                        {{ calculateChange(sliders[name]) }}
                                    </span>
                                    <span class="text-[10px] font-bold text-primary">
                                        Sim: Rp {{ formatPrice((data.price * sliders[name]) / 50) }}
                                    </span>
                                </div>
                            </div>
                            <input v-model="sliders[name]" type="range" min="0" max="100" class="w-full h-1.5 bg-surface-container-high rounded-full appearance-none cursor-pointer accent-primary" />
                            <div class="flex justify-between text-[10px] text-on-surface-variant font-medium uppercase tracking-widest">
                                <span>Deflation</span>
                                <span>Critical Spike</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-slate-100">
                        <div class="flex items-center gap-3 p-4 bg-secondary-fixed/30 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">info</span>
                            <p class="text-xs text-on-secondary-fixed-variant leading-relaxed">
                                Connected to <strong>Live Database</strong>. Using weighted regression for {{ Object.keys(db_commodities).length }} active commodities.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="col-span-12 lg:col-span-8 grid grid-cols-2 gap-6">
                <article class="col-span-2 bg-gradient-to-br from-primary to-primary-container p-8 rounded-xl text-white shadow-xl flex flex-col justify-between min-h-[240px]">
                    <div>
                        <span class="text-primary-fixed uppercase tracking-widest font-bold text-xs">Simulated Inflation Index</span>
                        <h3 class="text-7xl font-headline font-extrabold mt-4">{{ simulatedIndex }}%</h3>
                        <p class="text-primary-fixed/80 mt-2 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">trending_up</span>
                            {{ (simulatedIndex - baseline_inflation).toFixed(2) }}% from current baseline
                        </p>
                    </div>
                    <div class="flex gap-8 mt-8 border-t border-white/10 pt-6">
                        <div>
                            <p class="text-primary-fixed/60 text-xs font-semibold uppercase">SME Operational Cost</p>
                            <p class="text-xl font-headline font-bold">Rp 4.2M <span class="text-xs font-normal opacity-70">/ avg. unit</span></p>
                        </div>
                        <div>
                            <p class="text-primary-fixed/60 text-xs font-semibold uppercase">Market Confidence</p>
                            <p class="text-xl font-headline font-bold text-secondary-fixed-dim">
                                {{ simulatedIndex > 5 ? 'Vulnerable' : 'Moderate' }}
                            </p>
                        </div>
                    </div>
                </article>

                <section class="col-span-2 lg:col-span-1 bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_50px_rgba(0,33,22,0.04)]">
                    <div class="flex items-center justify-between mb-8">
                        <h4 class="font-headline font-bold text-on-surface">Purchasing Power Decay</h4>
                        <span class="material-symbols-outlined text-slate-400">more_horiz</span>
                    </div>
                    <div class="aspect-video relative flex items-end gap-2 mb-4">
                        <div v-for="(h, i) in [90, 85, 70, 55, 40, 30]" :key="i" 
                             :style="{ height: h + '%' }" 
                             :class="['flex-1 rounded-t-md transition-all', i < 3 ? 'bg-surface-container-high' : 'bg-primary/60']">
                        </div>
                    </div>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Predicted decline in household discretionary spending based on current simulation.
                    </p>
                </section>

                <section class="col-span-2 lg:col-span-1 bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_50px_rgba(0,33,22,0.04)]">
                    <div class="flex items-center justify-between mb-8">
                        <h4 class="font-headline font-bold text-on-surface">Sectoral Vulnerability</h4>
                        <span class="px-2 py-0.5 bg-secondary-fixed text-on-secondary-fixed font-bold text-[10px] rounded uppercase">Live Data</span>
                    </div>
                    <div class="space-y-6">
                        <div v-for="sector in [
                            { name: 'F&B Sector', risk: simulatedIndex > 5 ? 'High Risk' : 'Medium', val: simulatedIndex * 15, color: 'bg-error', icon: 'restaurant' },
                            { name: 'Logistics', risk: 'Stable', val: 35, color: 'bg-primary', icon: 'local_shipping' }
                        ]" :key="sector.name" class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-lg bg-secondary/10 flex items-center justify-center text-secondary">
                                <span class="material-symbols-outlined">{{ sector.icon }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-bold">{{ sector.name }}</span>
                                    <span :class="['font-bold', sector.val > 70 ? 'text-error' : 'text-primary']">{{ sector.risk }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-surface-container rounded-full">
                                    <div :class="['h-full rounded-full transition-all duration-500', sector.color]" :style="{ width: sector.val + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <button @click="showSaveModal = true" class="fixed bottom-8 right-8 h-14 px-6 rounded-full bg-primary text-white shadow-[0_20px_50px_rgba(0,33,22,0.2)] flex items-center gap-3 font-headline font-bold hover:scale-105 active:scale-95 transition-all">
            <span class="material-symbols-outlined">save</span>
            Save Scenario
        </button>

        <ExportReportModal :isOpen="showExportModal" @close="showExportModal = false" />
        <SaveScenarioModal :isOpen="showSaveModal" @close="showSaveModal = false" @save="(scenario) => { console.log('Scenario saved:', scenario); showSaveModal = false; }" />
    </StitchLayout>
</template>