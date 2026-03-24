<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const selectedLog = ref(null);
const filterType = ref('all'); // all, create, update, delete, verify

// Mock audit logs
const logs = ref([
    {
        id: 1,
        action: 'CREATE',
        commodity: 'Cabai Merah',
        region: 'DKI Jakarta',
        price: 48200,
        timestamp: '2024-01-15 14:32:45',
        user: 'system@pidi.id',
        hash: 'a3f5e8d2c1b9f4e7a6d9c2b5f8e1a4d7',
        status: 'verified',
        ipAddress: '192.168.1.100',
    },
    {
        id: 2,
        action: 'UPDATE',
        commodity: 'Daging Ayam',
        region: 'Jawa Timur',
        price: 36800,
        timestamp: '2024-01-15 14:30:12',
        user: 'market-collector@pidi.id',
        hash: 'f8e7d6c5b4a3f2e1d0c9b8a7f6e5d4c3',
        status: 'verified',
        ipAddress: '10.0.0.50',
    },
    {
        id: 3,
        action: 'VERIFY',
        commodity: 'Minyak Goreng',
        region: 'Sumatera Utara',
        price: 17200,
        timestamp: '2024-01-15 14:25:33',
        user: 'blockchain@pidi.id',
        hash: 'b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7',
        status: 'verified',
        ipAddress: '172.16.0.1',
    },
    {
        id: 4,
        action: 'DELETE',
        commodity: 'Bawang Putih',
        region: 'Jawa Barat',
        price: 21500,
        timestamp: '2024-01-15 14:20:15',
        user: 'admin@pidi.id',
        hash: 'c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8',
        status: 'archived',
        ipAddress: '203.0.113.45',
    },
    {
        id: 5,
        action: 'CREATE',
        commodity: 'Beras Putih',
        region: 'DKI Jakarta',
        price: 14500,
        timestamp: '2024-01-15 14:15:22',
        user: 'system@pidi.id',
        hash: 'd4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9',
        status: 'verified',
        ipAddress: '192.168.1.100',
    },
]);

const filteredLogs = computed(() => {
    if (filterType.value === 'all') {
        return logs.value;
    }
    return logs.value.filter(log => log.action === filterType.value.toUpperCase());
});

const closeModal = () => {
    selectedLog.value = null;
    emit('close');
};

const getActionColor = (action) => {
    switch(action) {
        case 'CREATE': return 'bg-green-100 text-green-800';
        case 'UPDATE': return 'bg-blue-100 text-blue-800';
        case 'DELETE': return 'bg-red-100 text-red-800';
        case 'VERIFY': return 'bg-purple-100 text-purple-800';
        default: return 'bg-slate-100 text-slate-800';
    }
};

const getActionIcon = (action) => {
    switch(action) {
        case 'CREATE': return 'add';
        case 'UPDATE': return 'edit';
        case 'DELETE': return 'delete';
        case 'VERIFY': return 'verified';
        default: return 'help';
    }
};

const getStatusColor = (status) => {
    return status === 'verified' ? 'text-green-600' : 'text-slate-600';
};
</script>

<template>
    <!-- Modal Overlay -->
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click="closeModal">
            <transition name="slide">
                <div v-if="isOpen" class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
                    <!-- Header -->
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                        <div>
                            <h2 class="text-2xl font-bold font-headline text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">history</span>
                                Audit Trail
                            </h2>
                            <p class="text-sm text-on-surface-variant">Immutable blockchain-verified event log</p>
                        </div>
                        <button @click="closeModal" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto">
                        <!-- Filter Bar -->
                        <div class="sticky top-0 px-8 py-4 bg-slate-50 border-b border-slate-100 flex gap-2">
                            <button 
                                v-for="type in ['all', 'create', 'update', 'delete', 'verify']"
                                :key="type"
                                @click="filterType = type"
                                :class="[
                                    'px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all',
                                    filterType === type
                                    ? 'bg-primary text-white'
                                    : 'bg-white text-on-surface-variant hover:bg-slate-100 border border-slate-200'
                                ]">
                                {{ type }}
                            </button>
                            <span class="ml-auto flex items-center text-xs text-on-surface-variant font-medium">
                                {{ filteredLogs.length }} entries
                            </span>
                        </div>

                        <!-- Detail View -->
                        <div v-if="selectedLog" class="p-8 space-y-6">
                            <button @click="selectedLog = null" class="flex items-center gap-2 text-primary hover:text-primary-dark transition-colors mb-4">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                <span class="text-sm font-bold">Back to Logs</span>
                            </button>

                            <!-- Action Badge -->
                            <div :class="['inline-block px-4 py-2 rounded-lg font-bold text-sm uppercase tracking-widest', getActionColor(selectedLog.action)]">
                                <span class="material-symbols-outlined text-sm align-middle mr-1">{{ getActionIcon(selectedLog.action) }}</span>
                                {{ selectedLog.action }}
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase mb-1">Commodity</p>
                                    <p class="text-lg font-bold">{{ selectedLog.commodity }}</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase mb-1">Region</p>
                                    <p class="text-lg font-bold">{{ selectedLog.region }}</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase mb-1">Price (Rp)</p>
                                    <p class="text-lg font-bold">{{ selectedLog.price.toLocaleString() }}</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase mb-1">Timestamp</p>
                                    <p class="text-sm font-bold">{{ selectedLog.timestamp }}</p>
                                </div>
                            </div>

                            <!-- User & IP Info -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase mb-1">User Account</p>
                                    <p class="text-sm font-bold text-on-surface">{{ selectedLog.user }}</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-bold uppercase mb-1">IP Address</p>
                                    <p class="text-sm font-bold font-mono text-on-surface">{{ selectedLog.ipAddress }}</p>
                                </div>
                            </div>

                            <!-- Blockchain Verification -->
                            <div class="bg-slate-900 text-white p-6 rounded-lg font-mono text-xs space-y-3">
                                <h4 class="text-sm font-bold text-slate-300 mb-4">Blockchain Verification</h4>
                                <div class="flex items-start gap-2">
                                    <span class="text-slate-400 flex-shrink-0">TX Hash:</span>
                                    <span class="text-blue-300 break-all">{{ selectedLog.hash }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-slate-400 flex-shrink-0">Status:</span>
                                    <span :class="[getStatusColor(selectedLog.status), 'font-bold uppercase']">{{ selectedLog.status }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-slate-400 flex-shrink-0">Entry:</span>
                                    <span class="text-green-300">IMMUTABLE</span>
                                </div>
                            </div>
                        </div>

                        <!-- List View -->
                        <div v-else class="p-8 space-y-3">
                            <button
                                v-for="log in filteredLogs"
                                :key="log.id"
                                @click="selectedLog = log"
                                class="w-full p-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg transition-all text-left group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span :class="['px-2 py-1 rounded text-xs font-bold uppercase', getActionColor(log.action)]">
                                                <span class="material-symbols-outlined text-xs align-middle">{{ getActionIcon(log.action) }}</span>
                                                {{ log.action }}
                                            </span>
                                            <span class="text-sm font-bold text-on-surface">{{ log.commodity }}</span>
                                            <span class="text-xs text-on-surface-variant">{{ log.region }}</span>
                                        </div>
                                        <div class="flex items-center gap-4 text-xs text-on-surface-variant">
                                            <span>{{ log.timestamp }}</span>
                                            <span>by {{ log.user.split('@')[0] }}</span>
                                            <span :class="['font-semibold', getStatusColor(log.status)]" style="font-variation-settings: 'FILL' 1;">
                                                <span class="material-symbols-outlined text-[10px] align-middle">{{ log.status === 'verified' ? 'verified' : 'pending' }}</span>
                                                {{ log.status }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-colors">arrow_forward</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between sticky bottom-0">
                        <p class="text-xs text-on-surface-variant">All entries immutably recorded on Ethereum blockchain | SHA-256 hashing</p>
                        <button @click="closeModal" class="px-6 py-2 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary-dark transition-colors">
                            Close
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
