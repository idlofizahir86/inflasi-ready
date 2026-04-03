<script setup>
import { ref, computed } from 'vue';
import StitchLayout from '@/Layouts/StitchLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import Pagination from '@/Components/Pagination.vue';
import ExportCSV from '@/Components/ExportCSV.vue';
import FilterStreamModal from '@/Components/FilterStreamModal.vue';
import BatchCleanModal from '@/Components/BatchCleanModal.vue';
import AuditLogModal from '@/Components/AuditLogModal.vue';
import { Head } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();

const props = defineProps({
    commodities: Array
});

// Modal states
const showFilterModal = ref(false);
const showCleanModal = ref(false);
const showLogsModal = ref(false);

// Paging
const currentPage = ref(1);
const itemsPerPage = 10;
const filteredCommodities = ref(props.commodities || []);

const totalPages = computed(() => Math.ceil(filteredCommodities.value.length / itemsPerPage) || 1);

// Stats
const stats = ref([
    { label: t('Total Records'), value: '142,809', trend: '+12%', color: 'text-emerald-900' },
    { label: t('Outliers Flagged'), value: '1,402', trend: t('Auto-Corrected'), color: 'text-rose-600', border: 'border-l-4 border-rose-200' },
    { label: t('Data Integrity'), value: '99.8%', icon: 'verified', color: 'text-emerald-700' },
    { label: t('Latency'), value: '24ms', trend: t('Real-time'), color: 'text-blue-600' },
]);

const displayedCommodities = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredCommodities.value.slice(start, end);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price);
};

const handleFilteredUpdate = (filtered) => {
    filteredCommodities.value = filtered;
    currentPage.value = 1;
};

const handleBatchCleanComplete = (result) => {
    stats.value[0].value = result.recordsProcessed.toLocaleString();
    stats.value[1].value = result.outliersCorrected.toLocaleString();
};
</script>

<template>
    <Head :title="t('Data Center')" />
    <StitchLayout>
        <header class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase rounded tracking-widest border border-emerald-200">{{ t('Data Cleaning Pipe') }}</span>
                    <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 uppercase">
                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">shield</span>
                        {{ t('Blockchain Verified') }}
                    </span>
                </div>
                <h2 class="text-4xl font-headline font-extrabold text-slate-900 tracking-tight">
                    {{ t('Data Washing & Normalization') }}
                </h2>
                <p class="text-slate-500 mt-2 max-w-2xl font-body leading-relaxed">
                    {{ t('Refining raw agricultural data streams') }}
                </p>
            </div>
            <div class="flex gap-3">
                <button @click="showFilterModal = true" class="px-5 py-2.5 bg-white text-slate-700 font-headline text-sm font-bold rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2 border border-slate-200 shadow-sm active:scale-95">
                    <span class="material-symbols-outlined text-sm">filter_list</span>
                    {{ t('Filter Stream') }}
                </button>
                <button @click="showCleanModal = true" class="px-5 py-2.5 bg-emerald-800 text-white font-headline text-sm font-bold rounded-xl shadow-lg shadow-emerald-900/20 hover:bg-emerald-900 transition-all flex items-center gap-2 active:scale-95">
                    <span class="material-symbols-outlined text-sm">cleaning_services</span>
                    {{ t('Run Batch Clean') }}
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div v-for="stat in stats" :key="stat.label" :class="['bg-white p-6 rounded-2xl border border-slate-100 shadow-sm transition-hover hover:shadow-md', stat.border]">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">{{ stat.label }}</p>
                <div class="flex items-baseline gap-2">
                    <span :class="['text-3xl font-headline font-bold', stat.color]">{{ stat.value }}</span>
                    <span v-if="stat.trend" class="text-xs font-bold px-1.5 py-0.5 rounded bg-slate-50" :class="stat.color">{{ stat.trend }}</span>
                    <span v-if="stat.icon" class="material-symbols-outlined text-emerald-600 text-lg ml-1" style="font-variation-settings: 'FILL' 1;">{{ stat.icon }}</span>
                </div>
            </div>
        </div>

        <SearchFilter :items="props.commodities || []" :searchFields="['name', 'category']" @update:filtered="handleFilteredUpdate" />

        <section class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">
            <div class="px-8 py-6 flex items-center justify-between bg-slate-50/50 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-bold text-slate-500 uppercase">{{ t('Data Records') }}</span>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">{{ filteredCommodities.length }} {{ t('Items') }}</span>
                </div>
                <span class="text-xs text-slate-400">{{ t('Last updated') }}: 5 {{ t('mins ago') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-on-surface-variant">
                    <thead class="text-xs font-bold uppercase border-b border-slate-100 bg-slate-50/50">
                        <tr>
                            <th class="px-8 py-4">{{ t('Commodity') }}</th>
                            <th class="px-4 py-4">{{ t('Category') }}</th>
                            <th class="px-4 py-4">{{ t('Price (Rp)') }}</th>
                            <th class="px-4 py-4">{{ t('Region') }}</th>
                            <th class="px-4 py-4">{{ t('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in displayedCommodities" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                            <td class="px-8 py-4 font-semibold text-on-surface">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    {{ item.name }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded">{{ item.category }}</span>
                            </td>
                            <td class="px-4 py-4 font-bold text-on-surface">Rp {{ formatPrice(item.price) }}</td>
                            <td class="px-4 py-4">{{ typeof item.region === 'object' ? item.region?.name || item.region : item.region || 'All Regions' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">
                                    <span class="material-symbols-outlined text-[10px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                                    {{ t('Verified') }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <Pagination :currentPage="currentPage" :totalPages="totalPages" :totalItems="filteredCommodities.length" :itemsPerPage="itemsPerPage" @update:page="currentPage = $event" />
                <div class="flex items-center gap-6">
                    <ExportCSV :items="filteredCommodities" filename="commodity-data.csv" />
                    <div class="h-4 w-[1px] bg-slate-200"></div>
                    <button @click="showLogsModal = true" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">history</span>
                        {{ t('Logs') }}
                    </button>
                </div>
            </div>
        </section>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-12 gap-8">
            <div class="md:col-span-8 bg-slate-900 p-8 rounded-3xl relative overflow-hidden group border border-slate-800 shadow-2xl">
                <div class="relative z-10">
                    <h4 class="font-headline font-bold text-xl mb-6 flex items-center gap-3 text-white">
                        <span class="material-symbols-outlined text-emerald-400">token</span>
                        {{ t('Z-Score Washing Pipeline') }}
                    </h4>
                    <div class="space-y-4 font-body text-sm text-slate-300 leading-relaxed">
                        <p>Mengidentifikasi dan memperbaiki anomali harga dengan algoritma Z-Score otomatis. Setiap data point dianalisis terhadap moving average dengan threshold σ &gt; 3.0.</p>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-white/5 border border-slate-700 p-4 rounded-lg">
                                <span class="text-emerald-400 font-bold text-lg block">142,809</span>
                                <span class="text-xs text-slate-400">{{ t('Total Records') }}</span>
                            </div>
                            <div class="bg-white/5 border border-slate-700 p-4 rounded-lg">
                                <span class="text-rose-400 font-bold text-lg block">1,402</span>
                                <span class="text-xs text-slate-400">{{ t('Outliers Detected') }}</span>
                            </div>
                            <div class="bg-white/5 border border-slate-700 p-4 rounded-lg">
                                <span class="text-blue-400 font-bold text-lg block">99.8%</span>
                                <span class="text-xs text-slate-400">{{ t('Data Integrity') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute -right-12 -bottom-12 opacity-5 pointer-events-none group-hover:scale-110 transition-transform duration-1000">
                    <span class="material-symbols-outlined text-[300px]" style="font-variation-settings: 'FILL' 1;">data_check</span>
                </div>
            </div>

            <div class="md:col-span-4 bg-white rounded-3xl p-8 border border-slate-100 shadow-sm space-y-6">
                <div>
                    <span class="material-symbols-outlined text-emerald-600 mb-6 text-4xl shadow-sm">auto_awesome</span>
                    <h4 class="font-headline font-bold text-xl mb-4">{{ t('Cleaning Logic') }}</h4>
                    <ul class="space-y-4">
                        <li v-for="rule in ['Z-Score Analysis (σ > 3.0)', 'Market Correlation Check', 'Source Reputation Weighting']" :key="rule" class="flex items-center gap-3 text-sm font-medium text-emerald-900">
                            <span class="material-symbols-outlined text-emerald-600 text-lg" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            {{ rule }}
                        </li>
                    </ul>
                </div>
                <button class="mt-10 w-full py-3 bg-emerald-900 text-white font-headline font-bold rounded-xl text-sm hover:bg-emerald-950 transition-all active:scale-95 shadow-lg shadow-black/5">
                    {{ t('Edit Global Rules') }}
                </button>
            </div>
        </div>

        <!-- Modals -->
        <FilterStreamModal :isOpen="showFilterModal" @close="showFilterModal = false" />
        <BatchCleanModal :isOpen="showCleanModal" @close="showCleanModal = false" @runClean="handleBatchCleanComplete" />
        <AuditLogModal :isOpen="showLogsModal" @close="showLogsModal = false" />
    </StitchLayout>
</template>

<style scoped>
.font-headline { font-family: 'Lexend', sans-serif; }
.font-body { font-family: 'Inter', sans-serif; }
</style>
