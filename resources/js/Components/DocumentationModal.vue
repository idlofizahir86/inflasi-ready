<script setup>
import { ref } from 'vue';

defineProps({
    isOpen: Boolean,
});

defineEmits(['close']);

const selectedEndpoint = ref(0);

const endpoints = [
    {
        method: 'GET',
        path: '/prices/daily',
        description: 'Fetch daily commodity prices',
        params: [
            { name: 'limit', type: 'integer', required: false, description: 'Max records (default: 100)' },
            { name: 'offset', type: 'integer', required: false, description: 'Pagination offset' },
        ],
        example: 'curl -H "X-API-KEY: your_key" https://api.inflasi-ready.id/prices/daily?limit=10',
    },
    {
        method: 'GET',
        path: '/regional/distribution',
        description: 'Get regional price distribution',
        params: [
            { name: 'region', type: 'string', required: true, description: 'Region name' },
            { name: 'commodity', type: 'string', required: false, description: 'Commodity filter' },
        ],
        example: 'curl -H "X-API-KEY: your_key" https://api.inflasi-ready.id/regional/distribution?region=Jawa%20Barat',
    },
    {
        method: 'POST',
        path: '/webhooks/alerts',
        description: 'Configure webhook alerts for price changes',
        params: [
            { name: 'webhook_url', type: 'string', required: true, description: 'Your webhook endpoint' },
            { name: 'threshold', type: 'number', required: true, description: 'Price change threshold %' },
        ],
        example: 'curl -X POST -H "X-API-KEY: your_key" -d "webhook_url=https://your.site/webhook&threshold=10" https://api.inflasi-ready.id/webhooks/alerts',
    },
];

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
};
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <transition name="slide">
                <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[85vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-gradient-to-r from-sky-600 to-sky-700 px-8 py-6 flex justify-between items-center rounded-t-2xl">
                        <div>
                            <p class="text-xs font-bold text-sky-100 uppercase tracking-widest">API Reference</p>
                            <h3 class="text-2xl font-headline font-bold text-white">Documentation</h3>
                        </div>
                        <button @click="$emit('close')" class="p-2 hover:bg-sky-500/30 rounded-lg transition text-white">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-8 space-y-8">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h4 class="font-bold text-blue-900 mb-2">📚 API Documentation</h4>
                            <p class="text-sm text-blue-800">Base URL: <code class="bg-white px-2 py-1 rounded">https://api.inflasi-ready.id/v1</code></p>
                            <p class="text-sm text-blue-800 mt-2">Authentication: <code class="bg-white px-2 py-1 rounded">Header: X-API-KEY</code></p>
                        </div>

                        <!-- Endpoint List -->
                        <div>
                            <h4 class="font-bold text-slate-900 mb-4">Available Endpoints</h4>
                            <div class="space-y-3">
                                <button 
                                    v-for="(ep, idx) in endpoints" 
                                    :key="idx"
                                    @click="selectedEndpoint = idx"
                                    :class="[
                                        'w-full p-4 rounded-lg text-left transition border-2 flex items-center gap-3',
                                        selectedEndpoint === idx 
                                            ? 'bg-sky-50 border-sky-500' 
                                            : 'bg-white border-slate-200 hover:border-slate-300'
                                    ]"
                                >
                                    <span :class="['px-2 py-1 rounded text-xs font-bold text-white', ep.method === 'GET' ? 'bg-blue-600' : 'bg-green-600']">{{ ep.method }}</span>
                                    <div class="flex-1">
                                        <p class="font-bold text-slate-900">{{ ep.path }}</p>
                                        <p class="text-xs text-slate-500">{{ ep.description }}</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Selected Endpoint Details -->
                        <div class="bg-slate-50 rounded-lg p-6 space-y-4">
                            <h4 class="font-bold text-slate-900">{{ endpoints[selectedEndpoint].path }}</h4>
                            
                            <div>
                                <p class="text-sm font-bold text-slate-700 mb-2">Parameters:</p>
                                <div class="space-y-2">
                                    <div v-for="param in endpoints[selectedEndpoint].params" :key="param.name" class="bg-white p-3 rounded border border-slate-200">
                                        <p class="font-mono text-xs font-bold text-sky-600">{{ param.name }}<span v-if="param.required" class="text-red-600"> *</span></p>
                                        <p class="text-xs text-slate-600">{{ param.description }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="text-sm font-bold text-slate-700 mb-2">Example:</p>
                                <div class="bg-slate-800 text-slate-100 p-4 rounded font-mono text-xs overflow-x-auto flex items-center justify-between">
                                    <code>{{ endpoints[selectedEndpoint].example }}</code>
                                    <button @click="copyToClipboard(endpoints[selectedEndpoint].example)" class="ml-4 p-2 hover:bg-slate-700 rounded transition">
                                        <span class="material-symbols-outlined text-sm text-slate-300">content_copy</span>
                                    </button>
                                </div>
                            </div>
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
</style>
