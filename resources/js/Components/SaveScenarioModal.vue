<script setup>
import { ref } from 'vue';

defineProps({
    isOpen: Boolean,
    scenarioData: Object,
});

defineEmits(['close', 'save']);

const scenarioName = ref('New Scenario');
const description = ref('');
const isPublic = ref(false);

const saveScenario = () => {
    const scenario = {
        name: scenarioName.value,
        description: description.value,
        isPublic: isPublic.value,
        data: this.scenarioData,
        createdAt: new Date().toISOString(),
    };
    
    this.$emit('save', scenario);
};
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <transition name="slide">
                <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full mx-4">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 flex justify-between items-center rounded-t-2xl">
                        <div>
                            <p class="text-xs font-bold text-blue-100 uppercase tracking-widest">Save Simulation</p>
                            <h3 class="text-2xl font-headline font-bold text-white">Save Scenario</h3>
                        </div>
                        <button @click="$emit('close')" class="p-2 hover:bg-blue-500/30 rounded-lg transition text-white">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-2">Nama Scenario</label>
                            <input v-model="scenarioName" type="text" placeholder="Masukkan nama scenario..." class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-900 mb-2">Deskripsi</label>
                            <textarea v-model="description" placeholder="Jelaskan scenario ini..." rows="4" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg">
                            <input v-model="isPublic" type="checkbox" class="w-4 h-4" id="public-scenario">
                            <label for="public-scenario" class="text-sm font-medium text-slate-700 cursor-pointer">
                                Buat scenario public (dapat dilihat user lain)
                            </label>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-green-900">
                                <span class="font-bold">✓ Scenario akan disimpan</span> dengan semua parameter simulasi
                            </p>
                            <p class="text-xs text-green-800 mt-2">
                                Anda dapat memuat ulang scenario ini kapan saja untuk analisis lebih lanjut
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex gap-3 justify-end rounded-b-2xl">
                        <button @click="$emit('close')" class="px-6 py-2.5 border border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-100 transition">
                            Cancel
                        </button>
                        <button @click="saveScenario; $emit('close')" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                            Save Scenario
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
