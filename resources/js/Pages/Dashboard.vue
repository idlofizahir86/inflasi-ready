<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import StitchLayout from '@/Layouts/StitchLayout.vue';
import PriceTrendChart from '@/Components/PriceTrendChart.vue';
import RegionalMapModal from '@/Components/RegionalMapModal.vue';
import BlockchainVerificationModal from '@/Components/BlockchainVerificationModal.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    all_regions: Array,
    selected_region_id: [String, Number],
    stats: Object,
    db_commodities: Array,
    chart_data: Array
});

const changeRegion = (event) => {
    router.get('/', { region_id: event.target.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const showMapModal = ref(false);
const showVerificationModal = ref(false);

// FIX: Mengambil data langsung dari all_regions agar Modal tidak "Loading..."
const regionsForModal = computed(() => {
    return props.all_regions.map(r => ({
        ...r,
        status: r.status || 'STABLE', // Fallback status
        color: r.color || 'bg-primary'
    }));
});

const getStatusClasses = (status) => {
    switch(status) {
        case 'CRITICAL': return 'bg-error/10 text-error';
        case 'MODERATE': return 'bg-orange-100 text-orange-700';
        case 'STABLE': return 'bg-primary/10 text-primary';
        default: return 'bg-slate-100 text-slate-700';
    }
};

const getDotClasses = (status) => {
    switch(status) {
        case 'CRITICAL': return 'bg-error';
        case 'MODERATE': return 'bg-orange-400';
        case 'STABLE': return 'bg-primary';
        default: return 'bg-slate-400';
    }
};

// Gunakan helper ini untuk memastikan kita mengolah angka
const formatNumber = (value) => {
    if (!value) return 0;
    // Hapus simbol % jika ada, lalu ubah ke Float
    const num = typeof value === 'string' ? parseFloat(value.replace('%', '')) : value;
    return isNaN(num) ? 0 : num;
};

const regionsPreview = computed(() => {
    // Filter hanya yang statusnya 'CRITICAL'
    const criticalRegions = props.all_regions.filter(region => region.status === 'CRITICAL');
    
    // Jika data CRITICAL lebih dari 5, ambil 5 saja. 
    // Jika kurang dari 5, tampilkan apa adanya.
    return criticalRegions.slice(0, 5);
});
const marketBaskets = computed(() => props.db_commodities);
</script>

<template>
    <Head title="Nasional Overview - Inflasi-Ready" />

    <StitchLayout>
        <section class="mb-12">
            <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-on-surface tracking-tight font-headline">Nasional Overview</h2>
                    <p class="text-on-surface-variant font-body">Monitoring: <span class="text-primary font-bold">{{ props.stats.region_display }}</span></p>
                </div>

                <div class="relative inline-block w-full md:w-64">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-primary mb-1 block ml-1">Wilayah Pantauan</label>
                    <select 
                        :value="props.selected_region_id"
                        @change="changeRegion"
                        class="w-full bg-surface-container-lowest border-none rounded-lg py-3 px-4 shadow-sm text-sm font-bold focus:ring-2 focus:ring-primary cursor-pointer"
                    >
                        <option value="national">🇮🇩 Nasional (Seluruh Indonesia)</option>
                        <option v-for="reg in props.all_regions" :key="reg.id" :value="reg.id">
                            {{ reg.name }}
                        </option>
                    </select>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="relative overflow-hidden bg-white/60 backdrop-blur-xl border border-white/40 p-8 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] group transition-all hover:-translate-y-1">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-colors"></div>
                    
                    <p class="text-on-surface-variant/80 text-xs font-bold uppercase tracking-widest mb-2">Inflation Rate</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-5xl font-black text-primary font-headline tracking-tighter">{{ props.stats.inflation }}%</h3>
                        <span :class="['flex items-center text-sm font-bold', formatNumber(props.stats.inflation_trend) > 0 ? 'text-error' : 'text-primary']">
                            <span class="material-symbols-outlined text-xs">{{ formatNumber(props.stats.inflation_trend) > 0 ? 'north' : 'south' }}</span>
                            {{ Math.abs(formatNumber(props.stats.inflation_trend)) }}%
                        </span>
                    </div>
                </div>

                <div class="relative p-[1px] rounded-2xl bg-gradient-to-br from-error/20 via-transparent to-transparent shadow-sm group hover:from-error/40 transition-all">
                    <div class="bg-surface-container-lowest p-8 rounded-[15px] h-full">
                        <p class="text-on-surface-variant text-xs font-bold uppercase tracking-widest mb-1">Top Rising</p>
                        <h4 class="text-on-surface font-bold text-lg mb-3">{{ props.stats.top_name }}</h4>
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-black text-on-surface font-headline">Rp {{ Number(formatNumber(props.stats.top_price)).toLocaleString('id-ID') }}</h3>
                            <div class="px-3 py-1.5 bg-error text-white text-[10px] font-black rounded-lg shadow-lg shadow-error/30 animate-pulse">
                                +{{ formatNumber(props.stats.top_trend) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-primary p-8 rounded-2xl shadow-[0_20px_40px_rgba(var(--m3-sys-light-primary-rgb),0.2)] relative overflow-hidden group">
                    <svg class="absolute right-0 bottom-0 opacity-10 group-hover:scale-110 transition-transform duration-700" width="150" height="150" viewBox="0 0 100 100">
                        <circle cx="100" cy="100" r="80" fill="white" />
                        <circle cx="100" cy="100" r="50" fill="white" />
                    </svg>

                    <p class="text-white/70 text-xs font-bold uppercase tracking-widest mb-2">AI Accuracy</p>
                    <h3 class="text-5xl font-black text-white font-headline tracking-tighter relative z-10">{{ props.stats.accuracy_score || '99.4' }}%</h3>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 mb-12">
            <div class="lg:col-span-3">
                <PriceTrendChart :chartData="props.chart_data" :commodities="props.db_commodities" />
            </div>

            <section class="lg:col-span-2 bg-surface-container-low rounded-lg p-8 overflow-hidden relative group">
                <div class="relative z-10">
                    <h3 class="text-xl font-bold font-headline text-on-surface mb-2">Region Hotspots</h3>
                    <p class="text-sm text-on-surface-variant mb-6">Wilayah dengan fluktuasi harga tertinggi</p>

                    <div class="space-y-4">
                        <div v-for="region in regionsPreview" :key="region.id" 
                            class="flex items-center justify-between p-3 bg-surface-container-lowest rounded-lg border border-transparent hover:border-slate-200 transition-all shadow-sm">
                            
                            <div class="flex items-center gap-3">
                                <div :class="['w-2.5 h-2.5 rounded-full', getDotClasses(region.status), region.status === 'CRITICAL' ? 'animate-pulse' : '']"></div>
                                <span class="text-sm font-bold text-on-surface">{{ region.name }}</span>
                            </div>

                            <span :class="['text-[10px] font-extrabold px-2.5 py-1 rounded-md uppercase tracking-wider', getStatusClasses(region.status)]">
                                {{ region.status }}
                            </span>
                        </div>

                        <div v-if="regionsPreview.length === 0" class="py-8 text-center bg-surface-container-lowest rounded-lg border border-dashed border-slate-300">
                            <span class="material-symbols-outlined text-primary/40 text-4xl mb-2">check_circle</span>
                            <p class="text-xs text-on-surface-variant font-medium">Semua wilayah saat ini terpantau STABLE</p>
                        </div>
                    </div>

                    <button @click="showMapModal = true" class="mt-8 w-full py-4 bg-white text-primary text-[10px] font-bold uppercase tracking-widest border border-primary/20 rounded-xl hover:bg-primary hover:text-white hover:shadow-lg hover:shadow-primary/20 transition-all duration-300">
                        Explore All 38 Provinces
                    </button>
                </div>

                <div class="absolute -right-12 -bottom-12 opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-1000">
                    <span class="material-symbols-outlined text-[300px] fill-1">map</span>
                </div>
            </section>
        </div>

        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold font-headline text-on-surface">Market Basket Monitor</h3>
                <Link href="/reports" class="text-sm font-bold text-primary flex items-center gap-1 hover:underline">
                    FULL REPORT <span class="material-symbols-outlined text-sm">analytics</span>
                </Link>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="basket in marketBaskets" :key="basket.name" 
                    class="bg-surface-container-lowest p-6 rounded-lg shadow-sm border border-transparent hover:border-primary/20 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 bg-secondary-fixed text-on-secondary-fixed text-[10px] font-bold rounded-full uppercase">
                            {{ basket.category }}
                        </span>
                    </div>
                    <h4 class="text-lg font-bold font-headline mb-1">{{ basket.name }}</h4>
                    <div class="flex items-baseline gap-1">
                        <span class="text-xs text-on-surface-variant">Rp</span>
                        <span class="text-xl font-bold font-headline">{{ basket.price }}</span>
                        <span :class="['text-[10px] font-bold ml-2', basket.trend.includes('+') ? 'text-error' : 'text-primary']">
                            ({{ basket.trend }})
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <RegionalMapModal :isOpen="showMapModal" :regions="regionsForModal" @close="showMapModal = false" />
        <BlockchainVerificationModal :isOpen="showVerificationModal" @close="showVerificationModal = false" />
    </StitchLayout>
</template>