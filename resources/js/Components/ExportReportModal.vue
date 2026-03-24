<script setup>
import { ref } from 'vue';

defineProps({
    isOpen: Boolean,
});

defineEmits(['close']);

const reportName = ref('Simulation Report');
const reportDate = new Date().toLocaleDateString('id-ID');
const selectedFormat = ref('pdf');

const generateReport = () => {
    const report = `
# ${reportName.value}
Generated: ${reportDate}
Format: ${selectedFormat.value.toUpperCase()}

## Simulation Summary
- Baseline Inflation: 2.5%
- Simulated Index: 3.2%
- Delta: +0.7%

## Commodity Impact Analysis
[Detailed analysis table]

## Recommendations
[Analysis and recommendations]
    `;
    
    // Create download
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(report));
    element.setAttribute('download', `${reportName.value}.txt`);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
};
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <transition name="slide">
                <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full mx-4">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-6 flex justify-between items-center rounded-t-2xl">
                        <div>
                            <p class="text-xs font-bold text-emerald-100 uppercase tracking-widest">Export</p>
                            <h3 class="text-2xl font-headline font-bold text-white">Export Report</h3>
                        </div>
                        <button @click="$emit('close')" class="p-2 hover:bg-emerald-500/30 rounded-lg transition text-white">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-2">Nama Report</label>
                            <input v-model="reportName" type="text" placeholder="Masukkan nama report..." class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-3">Format Export</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label v-for="fmt in ['pdf', 'csv', 'json']" :key="fmt" class="flex items-center gap-2 p-3 border-2 rounded-lg cursor-pointer transition" :class="selectedFormat === fmt ? 'border-emerald-600 bg-emerald-50' : 'border-slate-200 hover:border-slate-300'">
                                    <input v-model="selectedFormat" :value="fmt" type="radio" class="w-4 h-4">
                                    <span class="text-sm font-bold uppercase">{{ fmt }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-900">
                                <span class="font-bold">📅 Tanggal Report:</span> {{ reportDate }}
                            </p>
                            <p class="text-sm text-blue-900 mt-2">
                                <span class="font-bold">📊 Included Data:</span> Baseline, simulation results, commodity impact analysis
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex gap-3 justify-end rounded-b-2xl">
                        <button @click="$emit('close')" class="px-6 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-100 transition">
                            Cancel
                        </button>
                        <button @click="generateReport; $emit('close')" class="px-6 py-2.5 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition">
                            Download
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
