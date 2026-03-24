<script setup>
import { ref } from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'runClean']);

const cleaningStatus = ref('idle'); // idle, running, success, error
const cleaningProgress = ref(0);
const processedRecords = ref(0);
const outliersCorrected = ref(0);
const timeTaken = ref(0);

const closeModal = () => {
    if (cleaningStatus.value !== 'running') {
        cleaningStatus.value = 'idle';
        emit('close');
    }
};

const startCleaning = () => {
    cleaningStatus.value = 'running';
    processedRecords.value = 0;
    outliersCorrected.value = 0;
    cleaningProgress.value = 0;
    timeTaken.value = 0;

    // Simulate cleaning process
    let progress = 0;
    const startTime = Date.now();
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        cleaningProgress.value = Math.min(progress, 100);
        processedRecords.value = Math.floor(cleaningProgress.value * 1428.09); // 142,809 total
        outliersCorrected.value = Math.floor(processedRecords.value * 0.0098); // ~1.4% outliers
        timeTaken.value = ((Date.now() - startTime) / 1000).toFixed(1);

        if (progress >= 100) {
            clearInterval(interval);
            cleaningStatus.value = 'success';
            emit('runClean', {
                recordsProcessed: processedRecords.value,
                outliersCorrected: outliersCorrected.value,
                timeTaken: parseFloat(timeTaken.value),
            });
        }
    }, 200);
};

const resetCleaning = () => {
    cleaningStatus.value = 'idle';
    cleaningProgress.value = 0;
    processedRecords.value = 0;
    outliersCorrected.value = 0;
    timeTaken.value = 0;
};
</script>

<template>
    <!-- Modal Overlay -->
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click="cleaningStatus !== 'running' && closeModal()">
            <transition name="slide">
                <div v-if="isOpen" class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
                    <!-- Header -->
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                        <div>
                            <h2 class="text-2xl font-bold font-headline text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">cleaning_services</span>
                                Data Washing Pipeline
                            </h2>
                            <p class="text-sm text-on-surface-variant">Run Z-Score outlier detection and temporal smoothing</p>
                        </div>
                        <button v-if="cleaningStatus !== 'running'" @click="closeModal" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-8 space-y-6">
                        <!-- Idle State: Configuration -->
                        <div v-if="cleaningStatus === 'idle'" class="space-y-6">
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="text-sm font-bold text-blue-900 mb-2">Cleaning Algorithm</h4>
                                <ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
                                    <li>Z-Score Outlier Detection (σ &gt; 3.0)</li>
                                    <li>Temporal Smoothing (7-day moving average)</li>
                                    <li>Duplicate Record Removal</li>
                                    <li>Blockchain Hash Verification</li>
                                </ul>
                            </div>

                            <div>
                                <label class="block text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">
                                    <span class="material-symbols-outlined text-sm align-middle">tune</span>
                                    Cleaning Parameters
                                </label>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                                        <span class="text-sm font-medium">Z-Score Threshold</span>
                                        <span class="font-bold text-primary">3.0σ</span>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                                        <span class="text-sm font-medium">Smoothing Window</span>
                                        <span class="font-bold text-primary">7 days</span>
                                    </div>
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                                        <span class="text-sm font-medium">Target Records</span>
                                        <span class="font-bold text-primary">142,809</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <p class="text-xs text-green-800">
                                    ✓ All preprocessing checks passed. Ready to run batch cleaning.
                                </p>
                            </div>
                        </div>

                        <!-- Running State: Progress -->
                        <div v-else-if="cleaningStatus === 'running'" class="space-y-6">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4 animate-pulse">
                                    <span class="material-symbols-outlined text-3xl text-blue-600 animate-spin">hourglass_top</span>
                                </div>
                                <h4 class="text-lg font-bold text-on-surface mb-2">Processing Data Stream</h4>
                                <p class="text-sm text-on-surface-variant">Please wait while we clean and normalize...</p>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold">Overall Progress</span>
                                    <span class="text-sm font-bold text-primary">{{ cleaningProgress.toFixed(1) }}%</span>
                                </div>
                                <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-primary to-secondary transition-all duration-500" :style="{ width: `${cleaningProgress}%` }"></div>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-medium mb-1">Records Processed</p>
                                    <p class="text-2xl font-bold text-on-surface">{{ processedRecords.toLocaleString() }}</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-medium mb-1">Outliers Corrected</p>
                                    <p class="text-2xl font-bold text-error">{{ outliersCorrected.toLocaleString() }}</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-medium mb-1">Time Elapsed</p>
                                    <p class="text-lg font-bold text-on-surface">{{ timeTaken }}s</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                    <p class="text-xs text-on-surface-variant font-medium mb-1">Records/sec</p>
                                    <p class="text-lg font-bold text-primary">{{ (processedRecords / timeTaken).toFixed(0) }}</p>
                                </div>
                            </div>

                            <!-- Processing Steps -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                    <span class="material-symbols-outlined text-green-600" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span class="text-sm text-green-800">Preprocessing validation</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <span class="material-symbols-outlined text-blue-600 animate-spin">hourglass_top</span>
                                    <span class="text-sm text-blue-800">Z-Score outlier detection in progress...</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-slate-100 rounded-lg border border-slate-300">
                                    <span class="material-symbols-outlined text-slate-400">pending</span>
                                    <span class="text-sm text-slate-600">Temporal smoothing</span>
                                </div>
                            </div>
                        </div>

                        <!-- Success State -->
                        <div v-else-if="cleaningStatus === 'success'" class="space-y-6">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                                    <span class="material-symbols-outlined text-4xl text-green-600" style="font-variation-settings: 'FILL' 1;">verified</span>
                                </div>
                                <h4 class="text-lg font-bold text-on-surface mb-2">Cleaning Complete!</h4>
                                <p class="text-sm text-on-surface-variant">Data pipeline successfully processed and normalized</p>
                            </div>

                            <!-- Final Stats -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                    <p class="text-xs text-on-surface-variant font-medium mb-1">Total Records</p>
                                    <p class="text-2xl font-bold text-green-700">{{ processedRecords.toLocaleString() }}</p>
                                </div>
                                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                    <p class="text-xs text-on-surface-variant font-medium mb-1">Outliers Fixed</p>
                                    <p class="text-2xl font-bold text-red-700">{{ outliersCorrected.toLocaleString() }}</p>
                                </div>
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <p class="text-xs text-on-surface-variant font-medium mb-1">Processing Time</p>
                                    <p class="text-2xl font-bold text-blue-700">{{ timeTaken }}s</p>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                    <p class="text-xs text-on-surface-variant font-medium mb-1">Data Quality</p>
                                    <p class="text-2xl font-bold text-purple-700">99.8%</p>
                                </div>
                            </div>

                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="text-sm font-bold text-green-900 mb-2 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
                                    Summary
                                </h4>
                                <ul class="text-xs text-green-800 space-y-1 list-disc list-inside">
                                    <li>All outliers detected and corrected</li>
                                    <li>Data integrity verified via blockchain</li>
                                    <li>Ready for inflation index calculation</li>
                                    <li>Audit trail recorded immutably</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between sticky bottom-0">
                        <p class="text-xs text-on-surface-variant">Algorithm: Z-Score (σ&gt;3) + 7-day Temporal Smoothing</p>
                        <div v-if="cleaningStatus === 'idle'" class="flex gap-3">
                            <button @click="closeModal" class="px-6 py-2 border border-slate-300 text-slate-700 font-bold text-sm rounded-lg hover:bg-slate-50 transition-colors">
                                Cancel
                            </button>
                            <button @click="startCleaning" class="px-6 py-2 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary-dark transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">play_arrow</span>
                                Start Cleaning
                            </button>
                        </div>
                        <button v-else-if="cleaningStatus === 'success'" @click="() => { resetCleaning(); closeModal(); }" class="px-6 py-2 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary-dark transition-colors">
                            Done
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
