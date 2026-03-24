<script setup>
import { ref, computed } from 'vue';

defineProps({
    isOpen: Boolean,
});

defineEmits(['close']);

const selectedFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = 10;

const usageLogs = [
    { id: 1, timestamp: '2024-12-19 14:35:22', endpoint: 'GET /prices/daily', status: 200, responseTime: '245ms', ip: '192.168.1.1' },
    { id: 2, timestamp: '2024-12-19 14:34:18', endpoint: 'GET /regional/distribution', status: 200, responseTime: '312ms', ip: '192.168.1.1' },
    { id: 3, timestamp: '2024-12-19 14:32:45', endpoint: 'POST /webhooks/alerts', status: 201, responseTime: '156ms', ip: '192.168.1.2' },
    { id: 4, timestamp: '2024-12-19 14:30:12', endpoint: 'GET /prices/daily', status: 200, responseTime: '198ms', ip: '192.168.1.1' },
    { id: 5, timestamp: '2024-12-19 14:28:30', endpoint: 'GET /commodities/list', status: 200, responseTime: '89ms', ip: '192.168.1.3' },
    { id: 6, timestamp: '2024-12-19 14:26:15', endpoint: 'GET /prices/daily', status: 200, responseTime: '267ms', ip: '192.168.1.1' },
    { id: 7, timestamp: '2024-12-19 14:24:22', endpoint: 'GET /regional/distribution', status: 404, responseTime: '45ms', ip: '192.168.1.2' },
    { id: 8, timestamp: '2024-12-19 14:22:10', endpoint: 'POST /webhooks/alerts', status: 200, responseTime: '234ms', ip: '192.168.1.1' },
    { id: 9, timestamp: '2024-12-19 14:20:05', endpoint: 'GET /prices/daily', status: 200, responseTime: '289ms', ip: '192.168.1.4' },
    { id: 10, timestamp: '2024-12-19 14:18:45', endpoint: 'GET /commodities/forecast', status: 200, responseTime: '412ms', ip: '192.168.1.3' },
    { id: 11, timestamp: '2024-12-19 14:16:20', endpoint: 'GET /prices/daily', status: 200, responseTime: '201ms', ip: '192.168.1.1' },
    { id: 12, timestamp: '2024-12-19 14:14:30', endpoint: 'POST /webhooks/alerts', status: 400, responseTime: '67ms', ip: '192.168.1.5' },
];

const filteredLogs = computed(() => {
    if (selectedFilter.value === 'all') return usageLogs;
    if (selectedFilter.value === 'success') return usageLogs.filter(log => log.status >= 200 && log.status < 300);
    if (selectedFilter.value === 'error') return usageLogs.filter(log => log.status >= 400);
    return usageLogs;
});

const paginatedLogs = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredLogs.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() => Math.ceil(filteredLogs.value.length / itemsPerPage));

const getStatusColor = (status) => {
    if (status >= 200 && status < 300) return 'bg-green-100 text-green-700';
    if (status >= 400) return 'bg-red-100 text-red-700';
    return 'bg-slate-100 text-slate-700';
};

const getStatusIcon = (status) => {
    if (status >= 200 && status < 300) return 'check_circle';
    if (status >= 400) return 'error';
    return 'info';
};
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <transition name="slide">
                <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[85vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6 flex justify-between items-center rounded-t-2xl">
                        <div>
                            <p class="text-xs font-bold text-purple-100 uppercase tracking-widest">Activity Monitoring</p>
                            <h3 class="text-2xl font-headline font-bold text-white">Usage Logs</h3>
                        </div>
                        <button @click="$emit('close')" class="p-2 hover:bg-purple-500/30 rounded-lg transition text-white">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-8 space-y-6">
                        <!-- Stats Card -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                                <p class="text-xs font-bold text-green-700 uppercase">Successful Calls</p>
                                <p class="text-2xl font-headline font-bold text-green-900 mt-1">{{ usageLogs.filter(l => l.status < 400).length }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                                <p class="text-xs font-bold text-red-700 uppercase">Failed Calls</p>
                                <p class="text-2xl font-headline font-bold text-red-900 mt-1">{{ usageLogs.filter(l => l.status >= 400).length }}</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                <p class="text-xs font-bold text-blue-700 uppercase">Avg Response</p>
                                <p class="text-2xl font-headline font-bold text-blue-900 mt-1">245ms</p>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                                <p class="text-xs font-bold text-yellow-700 uppercase">Total Calls</p>
                                <p class="text-2xl font-headline font-bold text-yellow-900 mt-1">{{ usageLogs.length }}</p>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="flex gap-3">
                            <button 
                                @click="selectedFilter = 'all'; currentPage = 1"
                                :class="['px-4 py-2 rounded-lg font-bold transition text-sm', selectedFilter === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200']"
                            >
                                All Logs
                            </button>
                            <button 
                                @click="selectedFilter = 'success'; currentPage = 1"
                                :class="['px-4 py-2 rounded-lg font-bold transition text-sm', selectedFilter === 'success' ? 'bg-green-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200']"
                            >
                                ✓ Successful
                            </button>
                            <button 
                                @click="selectedFilter = 'error'; currentPage = 1"
                                :class="['px-4 py-2 rounded-lg font-bold transition text-sm', selectedFilter === 'error' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200']"
                            >
                                ✗ Errors
                            </button>
                        </div>

                        <!-- Logs Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-100 border-b-2 border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-bold text-slate-700">Timestamp</th>
                                        <th class="px-4 py-3 text-left font-bold text-slate-700">Endpoint</th>
                                        <th class="px-4 py-3 text-center font-bold text-slate-700">Status</th>
                                        <th class="px-4 py-3 text-right font-bold text-slate-700">Response Time</th>
                                        <th class="px-4 py-3 text-left font-bold text-slate-700">Client IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="log in paginatedLogs" :key="log.id" class="border-b border-slate-100 hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ log.timestamp }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-slate-700 font-bold">{{ log.endpoint }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span :class="['inline-flex items-center gap-1 px-2 py-1 rounded-lg font-bold text-xs', getStatusColor(log.status)]">
                                                <span class="material-symbols-outlined text-sm">{{ getStatusIcon(log.status) }}</span>
                                                {{ log.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono text-xs text-slate-600">{{ log.responseTime }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ log.ip }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-slate-600">Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredLogs.length) }} of {{ filteredLogs.length }} logs</p>
                            <div class="flex gap-2">
                                <button 
                                    @click="currentPage--" 
                                    :disabled="currentPage === 1"
                                    class="p-2 rounded-lg hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                >
                                    <span class="material-symbols-outlined text-slate-700">chevron_left</span>
                                </button>
                                <div class="flex gap-1">
                                    <button 
                                        v-for="page in totalPages" 
                                        :key="page"
                                        @click="currentPage = page"
                                        :class="['w-8 h-8 rounded-lg font-bold text-sm transition', currentPage === page ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200']"
                                    >
                                        {{ page }}
                                    </button>
                                </div>
                                <button 
                                    @click="currentPage++" 
                                    :disabled="currentPage === totalPages"
                                    class="p-2 rounded-lg hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                >
                                    <span class="material-symbols-outlined text-slate-700">chevron_right</span>
                                </button>
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
