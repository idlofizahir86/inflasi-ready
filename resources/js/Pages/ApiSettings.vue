<script setup>
import { ref } from 'vue';
import StitchLayout from '@/Layouts/StitchLayout.vue';
import DocumentationModal from '@/Components/DocumentationModal.vue';
import UsageLogModal from '@/Components/UsageLogModal.vue';
import { Head } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();

const apiKey = ref('ad_live_8842_2024_data_secure_9x');
const copied = ref(false);
const showDocModal = ref(false);
const showUsageModal = ref(false);

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(apiKey.value);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
};

const endpoints = [
    { method: 'GET', path: '/prices/daily', desc: 'Fetch the latest national average prices for strategic commodities.', icon: 'monitoring', color: 'bg-sky-100 text-sky-700' },
    { method: 'GET', path: '/regional/distribution', desc: 'Access province-level pricing data with historical volatility mapping.', icon: 'map', color: 'bg-sky-100 text-sky-700' },
    { method: 'POST', path: '/webhooks/alerts', desc: 'Configure real-time notifications for sudden price spikes exceeding 10%.', icon: 'webhook', color: 'bg-rose-100 text-rose-700' },
];

const datasets = [
    { name: 'Historical_2023.csv', type: 'CSV', size: '142 MB', icon: 'description' },
    { name: 'Commodity_Catalog.json', type: 'JSON', size: '12 MB', icon: 'data_object' },
];
</script>

<template>
    <Head :title="t('API & Data Integration')" />

    <StitchLayout>
        <div class="max-w-7xl mx-auto">
            <header class="mb-12">
                <div class="flex items-center gap-2 text-primary-container mb-2">
                    <span class="material-symbols-outlined text-sm">terminal</span>
                    <span class="text-xs font-bold tracking-widest uppercase font-label">{{ t('Developer Resources') }}</span>
                </div>
                <h2 class="text-4xl font-extrabold font-headline tracking-tight text-on-surface mb-4">{{ t('API & Data Integration') }}</h2>
                <p class="text-on-surface-variant max-w-2xl font-body leading-relaxed">
                    {{ t('Build resilient economic models') }}
                </p>
            </header>

            <div class="grid grid-cols-12 gap-6">
                
                <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
                    <section class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_50px_rgba(0,33,22,0.04)] h-full">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="p-3 bg-secondary-fixed rounded-lg text-on-secondary-fixed">
                                <span class="material-symbols-outlined">key</span>
                            </div>
                            <h3 class="font-headline font-bold text-lg">{{ t('Authentication') }}</h3>
                        </div>
                        <p class="text-sm text-on-surface-variant mb-6 font-body">
                            {{ t('Use this key in the header') }} <code class="bg-surface-container-high px-1 rounded">X-API-KEY</code> {{ t('for all authorized requests') }}.
                        </p>
                        
                        <div class="space-y-4">
                            <div class="text-xs font-label text-on-surface-variant mb-2 font-bold uppercase tracking-wider">{{ t('Production API Key') }}</div>
                            <div class="flex items-center justify-between bg-surface-container-high p-4 rounded-lg group">
                                <code class="text-xs font-mono font-medium truncate text-primary-container">{{ apiKey }}</code>
                                <button @click="copyToClipboard" class="p-2 hover:bg-white rounded-md transition-colors text-primary cursor-pointer active:scale-90">
                                    <span class="material-symbols-outlined text-lg">{{ copied ? 'check' : 'content_copy' }}</span>
                                </button>
                            </div>
                            <div class="flex gap-2 items-center mt-4">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] text-on-surface-variant font-medium">{{ t('Last active') }} 2 {{ t('minutes ago') }}</span>
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-slate-100">
                            <button class="w-full bg-gradient-to-br from-primary to-primary-container text-white py-3 rounded-lg font-headline font-semibold text-sm shadow-md hover:shadow-lg transition-all active:scale-95">
                                {{ t('Regenerate Key') }}
                            </button>
                        </div>
                    </section>
                </div>

                <div class="col-span-12 lg:col-span-8">
                    <section class="bg-[#1e293b] rounded-xl overflow-hidden shadow-2xl relative h-full">
                        <div class="flex items-center justify-between px-6 py-4 bg-slate-800/50 border-b border-slate-700">
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1.5">
                                    <div class="w-3 h-3 rounded-full bg-rose-500/50"></div>
                                    <div class="w-3 h-3 rounded-full bg-amber-500/50"></div>
                                    <div class="w-3 h-3 rounded-full bg-emerald-500/50"></div>
                                </div>
                                <span class="ml-4 text-xs font-mono text-slate-400">GET /v1/prices/daily</span>
                            </div>
                            <div class="flex gap-3">
                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[10px] font-bold border border-emerald-500/20 uppercase tracking-widest">200 OK</span>
                                <span class="text-xs font-mono text-slate-500">124ms</span>
                            </div>
                        </div>
                        <div class="p-8 font-mono text-sm leading-relaxed overflow-x-auto">
                            <pre class="text-emerald-400/90" style="text-shadow: 0 0 8px rgba(139, 214, 183, 0.3);">
{
  <span class="text-sky-300">"status"</span>: <span class="text-emerald-300">"success"</span>,
  <span class="text-sky-300">"timestamp"</span>: <span class="text-emerald-300">"2024-05-20T08:00:00Z"</span>,
  <span class="text-sky-300">"data"</span>: [
    {
      <span class="text-sky-300">"commodity"</span>: <span class="text-emerald-300">"Beras Medium"</span>,
      <span class="text-sky-300">"price"</span>: <span class="text-orange-300">14500</span>,
      <span class="text-sky-300">"currency"</span>: <span class="text-emerald-300">"IDR"</span>,
      <span class="text-sky-300">"region"</span>: <span class="text-emerald-300">"Jakarta Pusat"</span>
    },
    {
      <span class="text-sky-300">"commodity"</span>: <span class="text-emerald-300">"Cabai Merah"</span>,
      <span class="text-sky-300">"price"</span>: <span class="text-orange-300">42000</span>,
      <span class="text-sky-300">"change_percentage"</span>: <span class="text-orange-300">+2.4</span>
    }
  ]
}</pre>
                        </div>
                    </section>
                </div>

                <div class="col-span-12 lg:col-span-7">
                    <section class="bg-surface-container-low p-8 rounded-xl h-full">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="font-headline font-bold text-xl">{{ t('Documentation Overview') }}</h3>
                            <button @click="showDocModal = true" class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-bold hover:bg-primary-container transition">
                                {{ t('View Full Docs') }}
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div v-for="endpoint in endpoints" :key="endpoint.path" 
                                 @click="showDocModal = true"
                                 class="bg-white p-5 rounded-lg flex items-start gap-4 hover:shadow-md transition-shadow cursor-pointer group">
                                <div class="w-12 h-12 flex-shrink-0 bg-secondary-fixed rounded-lg flex items-center justify-center text-on-secondary-fixed group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined">{{ endpoint.icon }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span :class="['px-2 py-0.5 text-[10px] font-bold rounded', endpoint.color]">{{ endpoint.method }}</span>
                                        <h4 class="font-headline font-semibold text-sm">{{ endpoint.path }}</h4>
                                    </div>
                                    <p class="text-xs text-on-surface-variant font-body">{{ endpoint.desc }}</p>
                                </div>
                                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-span-12 lg:col-span-5">
                    <section class="bg-white p-8 rounded-xl border border-outline-variant/20 h-full flex flex-col justify-between shadow-sm">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-headline font-bold text-xl">{{ t('Raw Datasets') }}</h3>
                                <button @click="showUsageModal = true" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition">
                                    {{ t('View Logs') }}
                                </button>
                            </div>
                            <p class="text-sm text-on-surface-variant mb-8 font-body">{{ t('Download comprehensive historical records for offline analysis.') }}</p>
                            
                            <div class="space-y-6">
                                <div v-for="file in datasets" :key="file.name" class="flex items-center justify-between p-4 bg-surface rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-slate-400">{{ file.icon }}</span>
                                        <div>
                                            <div class="text-xs font-bold">{{ file.name }}</div>
                                            <div class="text-[10px] text-on-surface-variant uppercase">{{ file.type }} • {{ file.size }}</div>
                                        </div>
                                    </div>
                                    <button class="flex items-center gap-2 bg-secondary-container text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-secondary transition-colors active:scale-95">
                                        <span class="material-symbols-outlined text-sm">download</span>
                                        {{ t('Download') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 bg-primary-fixed p-6 rounded-lg">
                            <h4 class="font-headline font-bold text-on-primary-fixed mb-2 text-sm">{{ t('Need a Custom Feed?') }}</h4>
                            <p class="text-[11px] text-on-primary-fixed-variant mb-4 font-body leading-relaxed">
                                {{ t('Enterprise partners can request dedicated pipelines for high-frequency monitoring.') }}
                            </p>
                            <a href="#" class="text-xs font-bold text-primary-container flex items-center gap-1 hover:underline">
                                {{ t('Contact Enterprise Support') }}
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </section>
                </div>

            </div>

            <footer class="mt-24 pb-12 text-center">
                <p class="text-[10px] text-on-surface-variant font-bold tracking-widest uppercase mb-4">{{ t('Integrity in every byte') }}</p>
                <div class="flex justify-center gap-8 items-center grayscale opacity-50">
                    <div class="w-12 h-6 bg-slate-300 rounded"></div> <div class="w-12 h-6 bg-slate-300 rounded"></div>
                    <div class="w-12 h-6 bg-slate-300 rounded"></div>
                </div>
            </footer>
        </div>

        <DocumentationModal :isOpen="showDocModal" @close="showDocModal = false" />
        <UsageLogModal :isOpen="showUsageModal" @close="showUsageModal = false" />
    </StitchLayout>
</template>