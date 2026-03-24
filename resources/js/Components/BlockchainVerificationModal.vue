<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const verificationStatus = ref('idle'); // idle, verifying, success, failed
const selectedEntry = ref(null);

// Mock blockchain entries
const blockchainEntries = ref([
    {
        id: 1,
        hash: 'a3f5e8d2c1b9f4e7a6d9c2b5f8e1a4d7',
        timestamp: '2024-01-15 14:32:45',
        commodity: 'Cabai Merah',
        price: 48200,
        region: 'DKI Jakarta',
        verified: true,
        block: 2847291,
    },
    {
        id: 2,
        hash: 'f8e7d6c5b4a3f2e1d0c9b8a7f6e5d4c3',
        timestamp: '2024-01-15 14:30:12',
        commodity: 'Daging Ayam',
        price: 36800,
        region: 'Jawa Timur',
        verified: true,
        block: 2847290,
    },
    {
        id: 3,
        hash: 'b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7',
        timestamp: '2024-01-15 14:25:33',
        commodity: 'Minyak Goreng',
        price: 17200,
        region: 'Sumatera Utara',
        verified: true,
        block: 2847289,
    },
]);

const closeModal = () => {
    selectedEntry.value = null;
    verificationStatus.value = 'idle';
    emit('close');
};

const verifyEntry = (entry) => {
    selectedEntry.value = entry;
    verificationStatus.value = 'verifying';
    
    // Simulate verification process
    setTimeout(() => {
        verificationStatus.value = 'success';
    }, 2000);
};

const resetVerification = () => {
    selectedEntry.value = null;
    verificationStatus.value = 'idle';
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
                                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                                Blockchain Verification
                            </h2>
                            <p class="text-sm text-on-surface-variant">Verify market data integrity</p>
                        </div>
                        <button @click="closeModal" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-8">
                        <!-- Verification Detail View -->
                        <div v-if="selectedEntry">
                            <button @click="resetVerification" class="flex items-center gap-2 text-primary hover:text-primary-dark transition-colors mb-6">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                <span class="text-sm font-bold">Back to Entries</span>
                            </button>

                            <!-- Status Badge -->
                            <div class="mb-6">
                                <transition name="fade">
                                    <div v-if="verificationStatus === 'verifying'" class="p-6 bg-blue-50 border border-blue-200 rounded-lg text-center">
                                        <div class="flex justify-center mb-4">
                                            <div class="animate-spin">
                                                <span class="material-symbols-outlined text-4xl text-blue-600">hourglass_top</span>
                                            </div>
                                        </div>
                                        <p class="text-sm font-bold text-blue-900">Verifying blockchain entry...</p>
                                        <p class="text-xs text-blue-700 mt-2">Please wait while we confirm authenticity</p>
                                    </div>
                                    <div v-else-if="verificationStatus === 'success'" class="p-6 bg-primary/10 border border-primary rounded-lg">
                                        <div class="flex items-start gap-4">
                                            <div class="mt-1">
                                                <span class="material-symbols-outlined text-3xl text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-bold text-primary mb-1">Verification Successful</h4>
                                                <p class="text-sm text-on-surface-variant">This market data entry is authentic and has been immutably recorded on the blockchain.</p>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>

                            <!-- Entry Details -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                        <p class="text-xs text-on-surface-variant font-bold uppercase mb-2">Commodity</p>
                                        <p class="text-lg font-bold">{{ selectedEntry.commodity }}</p>
                                    </div>
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                        <p class="text-xs text-on-surface-variant font-bold uppercase mb-2">Price (Rp)</p>
                                        <p class="text-lg font-bold">{{ selectedEntry.price.toLocaleString() }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                        <p class="text-xs text-on-surface-variant font-bold uppercase mb-2">Region</p>
                                        <p class="text-lg font-bold">{{ selectedEntry.region }}</p>
                                    </div>
                                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                        <p class="text-xs text-on-surface-variant font-bold uppercase mb-2">Timestamp</p>
                                        <p class="text-sm font-bold">{{ selectedEntry.timestamp }}</p>
                                    </div>
                                </div>

                                <!-- Blockchain Details -->
                                <div class="bg-slate-900 text-white p-6 rounded-lg font-mono text-xs space-y-3">
                                    <div class="flex items-start gap-2">
                                        <span class="text-slate-400 flex-shrink-0">Block:</span>
                                        <span class="text-green-300 break-all">{{ selectedEntry.block }}</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="text-slate-400 flex-shrink-0">TX Hash:</span>
                                        <span class="text-blue-300 break-all">{{ selectedEntry.hash }}</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="text-slate-400 flex-shrink-0">Status:</span>
                                        <span class="text-emerald-300 font-bold">CONFIRMED</span>
                                    </div>
                                </div>

                                <!-- Verification Path -->
                                <div class="bg-primary/5 p-6 rounded-lg border border-primary/20">
                                    <h4 class="text-sm font-bold uppercase tracking-widest text-primary mb-4">Merkle Tree Verification</h4>
                                    <div class="space-y-2 text-xs font-mono">
                                        <div class="flex items-center gap-2 text-slate-600">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                            <span>Entry Hash: <span class="text-primary font-bold">✓ Valid</span></span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-600">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                            <span>Block Signature: <span class="text-primary font-bold">✓ Valid</span></span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-600">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                            <span>Timestamp: <span class="text-primary font-bold">✓ Valid</span></span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-600">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                            <span>Chain Continuity: <span class="text-primary font-bold">✓ Valid</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- List View -->
                        <div v-else>
                            <h3 class="text-lg font-bold font-headline text-on-surface mb-4">Recent Verified Entries</h3>
                            <div class="space-y-3">
                                <button 
                                    v-for="entry in blockchainEntries"
                                    :key="entry.id"
                                    @click="verifyEntry(entry)"
                                    class="w-full p-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg transition-all text-left group">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span v-if="entry.verified" class="material-symbols-outlined text-sm text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                                                <span class="font-bold text-on-surface">{{ entry.commodity }}</span>
                                                <span class="text-xs font-semibold text-on-surface-variant">{{ entry.region }}</span>
                                            </div>
                                            <div class="flex items-baseline gap-2">
                                                <span class="text-sm text-on-surface-variant">Rp</span>
                                                <span class="font-bold text-on-surface">{{ entry.price.toLocaleString() }}</span>
                                            </div>
                                            <p class="text-xs text-on-surface-variant mt-2">{{ entry.timestamp }}</p>
                                        </div>
                                        <span class="material-symbols-outlined text-slate-400 group-hover:text-primary transition-colors">arrow_forward</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between sticky bottom-0">
                        <p class="text-xs text-on-surface-variant">Powered by Ethereum Smart Contracts | SHA-256 Hashing</p>
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
