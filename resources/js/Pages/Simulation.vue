<script setup>
import { ref, computed, onMounted } from 'vue';
import StitchLayout from '@/Layouts/StitchLayout.vue';
import ExportReportModal from '@/Components/ExportReportModal.vue';
import SaveScenarioModal from '@/Components/SaveScenarioModal.vue';
import { Head } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();

const props = defineProps({
    db_commodities: Object,
    baseline_inflation: Number,
    all_commodities: Array
});

const sliders = ref({});
const showExportModal = ref(false);
const showSaveModal = ref(false);

// NEW: Smart Features State
const showAIChat = ref(false);
const aiMessages = ref([]);
const aiInput = ref('');
const aiLoading = ref(false);
const supplierRecommendations = ref([]);
const advancedCalculationResult = ref(null);
const showAdvancedPanel = ref(false);
const selectedCommodity = ref(null);
const advancedForm = ref({
    base_price: 0,
    simulated_price: 0,
    weight_in_basket: 0.5,
    seasonal_factor: 1.0,
    base_operational_cost: 0
});

onMounted(() => {
    Object.keys(props.db_commodities).forEach(name => {
        sliders.value[name] = 50;
    });
});

const calculateChange = (val) => {
    const change = (val - 50) * 2;
    return change > 0 ? `+${change.toFixed(1)}%` : `${change.toFixed(1)}%`;
};

const simulatedIndex = computed(() => {
    const base = props.baseline_inflation;
    const keys = Object.keys(sliders.value);
    
    if (keys.length === 0) return base.toFixed(2);

    const totalImpact = keys.reduce((acc, name) => {
        return acc + (sliders.value[name] - 50);
    }, 0);

    const impactFactor = (totalImpact / keys.length) * 0.08;
    return (base + impactFactor).toFixed(2);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(Math.round(price));
};

// NEW: AI Chat Methods
const sendAIMessage = async () => {
    if (!aiInput.value.trim()) return;

    const userMessage = aiInput.value;
    aiMessages.value.push({
        role: 'user',
        content: userMessage,
        timestamp: new Date()
    });

    aiInput.value = '';
    aiLoading.value = true;

    try {
        const response = await fetch(route('simulation.chat'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                message: userMessage,
                conversation_history: aiMessages.value.slice(0, -1).map(msg => ({
                    role: msg.role,
                    content: msg.content
                }))
            })
        });

        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            console.error('Failed to parse JSON response:', parseError);
            throw new Error('Invalid response format from server');
        }
        
        console.log('Chat API Response:', { status: response.status, success: data?.success });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${data?.message || 'Server error'}`);
        }
        
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response structure from server');
        }
        
        if (data.success && (data.reply || data.message)) {
            const replyText = data.reply || data.message;
            if (!replyText || typeof replyText !== 'string') {
                throw new Error('Invalid reply format from AI');
            }
            aiMessages.value.push({
                role: 'assistant',
                content: replyText,
                timestamp: new Date()
            });
        } else {
            const errorMsg = data.message || 'Maaf, AI Assistant tidak dapat memproses pesan Anda.';
            aiMessages.value.push({
                role: 'assistant',
                content: errorMsg,
                timestamp: new Date()
            });
        }
    } catch (error) {
        console.error('AI Chat Error Details:', error);
        const errorMessage = error?.message || 'Koneksi ke server gagal';
        aiMessages.value.push({
            role: 'assistant',
            content: `Maaf, terjadi kesalahan: ${errorMessage}`,
            timestamp: new Date()
        });
    } finally {
        aiLoading.value = false;
    }
};

const extractStoryAndSimulate = async () => {
    if (!aiInput.value.trim() || aiInput.value.length < 10) {
        alert(t('Cerita harus minimal 10 karakter'));
        return;
    }

    aiLoading.value = true;

    try {
        const response = await fetch(route('simulation.extract-story'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                story: aiInput.value
            })
        });

        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            console.error('Failed to parse JSON response:', parseError);
            throw new Error('Invalid response format from server');
        }
        
        console.log('Extract Story API Response:', { status: response.status, success: data?.success });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${data?.message || 'Server error'}`);
        }
        
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response structure from server');
        }
        
        if (data.success && data.extracted_parameters) {
            const extracted = data.extracted_parameters;
            
            // Validasi extracted parameters
            if (!extracted || typeof extracted !== 'object') {
                throw new Error('Invalid extracted parameters format');
            }
            
            // Update form dengan extracted parameters
            selectedCommodity.value = extracted.commodity_id || null;
            advancedForm.value.weight_in_basket = extracted.weight_in_basket || 0.5;
            advancedForm.value.seasonal_factor = extracted.seasonal_factor || 1.0;
            
            // Prompt user untuk input sisa parameter
            showAdvancedPanel.value = true;
            
            const businessType = extracted.business_type || 'bisnis Anda';
            const priceChange = extracted.price_change_percent || '?';
            
            aiMessages.value.push({
                role: 'assistant',
                content: `Saya telah menganalisis cerita Anda. Terdeteksi: ${businessType}, perubahan harga ${priceChange}%. Silakan masukkan harga dasar dan biaya operasional untuk menyelesaikan simulasi.`,
                timestamp: new Date()
            });
        } else {
            const errorMsg = data.message || 'Maaf, terjadi kesalahan saat menganalisis cerita Anda. Coba lagi dengan cerita yang lebih detail.';
            aiMessages.value.push({
                role: 'assistant',
                content: errorMsg,
                timestamp: new Date()
            });
        }
    } catch (error) {
        console.error('Extract Story Error Details:', error);
        const errorMessage = error?.message || 'Koneksi ke server gagal';
        aiMessages.value.push({
            role: 'assistant',
            content: `Maaf, terjadi kesalahan: ${errorMessage}`,
            timestamp: new Date()
        });
    } finally {
        aiLoading.value = false;
    }
};

// NEW: Advanced Calculation
const runAdvancedSimulation = async () => {
    if (!selectedCommodity.value || !advancedForm.value.base_price || !advancedForm.value.simulated_price || !advancedForm.value.base_operational_cost) {
        alert('Harap isi semua parameter yang diperlukan');
        return;
    }

    try {
        const response = await fetch(route('simulation.advanced-calculate'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                commodity_id: selectedCommodity.value,
                base_price: advancedForm.value.base_price,
                simulated_price: advancedForm.value.simulated_price,
                weight_in_basket: advancedForm.value.weight_in_basket,
                seasonal_factor: advancedForm.value.seasonal_factor,
                base_operational_cost: advancedForm.value.base_operational_cost
            })
        });

        const data = await response.json();

        if (data.success) {
            advancedCalculationResult.value = data.simulation_result;
            supplierRecommendations.value = data.supplier_recommendations || [];
        } else {
            alert('Simulasi gagal: ' + (data.message || 'Kesalahan tidak diketahui'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menjalankan simulasi. Silakan cek console untuk detail.');
    }
};

const simulateWithSupplier = async (supplier) => {
    if (!advancedCalculationResult.value) return;

    try {
        const response = await fetch(route('simulation.simulate-with-supplier'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                commodity_id: advancedCalculationResult.value.commodity.id,
                supplier_id: supplier.supplier_id,
                supplier_price: supplier.price || advancedCalculationResult.value.price_analysis.base_price,
                quantity: 1
            })
        });

        const data = await response.json();

        if (data.success) {
            advancedCalculationResult.value = data.data;
            aiMessages.value.push({
                role: 'assistant',
                content: `Simulasi dengan supplier berhasil! Total cost: Rp ${formatPrice(data.data.total_cost)}, Rekomendasi: ${data.data.recommendation}`,
                timestamp: new Date()
            });
        } else {
            alert('Re-simulasi gagal: ' + (data.message || 'Kesalahan tidak diketahui'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat re-simulasi. Silakan cek console untuk detail.');
    }
};

const getRiskColor = (impactScore) => {
    if (impactScore >= 70) return 'bg-error text-white';
    if (impactScore >= 40) return 'bg-warning text-white';
    if (impactScore >= 20) return 'bg-secondary text-white';
    return 'bg-primary text-white';
};
</script>

<template>
    <Head :title="t('Inflation Simulator')" />

    <StitchLayout>
        <header class="mb-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h2 class="text-4xl font-extrabold font-headline tracking-tight text-primary mb-2">{{ t('Inflation Simulator') }}</h2>
                    <p class="text-on-surface-variant max-w-2xl text-lg">{{ t('Model the impact of commodity price fluctuations from real-time database data.') }}</p>
                </div>
                <div class="flex gap-3">
                    <button @click="showAIChat = !showAIChat" class="px-5 py-2.5 rounded-lg border border-outline-variant text-primary font-medium hover:bg-surface-container transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined">smart_toy</span>
                        {{ t('AI Assistant') }}
                    </button>
                    <button @click="showExportModal = true" class="px-5 py-2.5 rounded-lg border border-outline-variant text-primary font-medium hover:bg-surface-container transition-colors">{{ t('Export Report') }}</button>
                    <button @click="Object.keys(sliders).forEach(k => sliders[k] = 50)" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium shadow-lg shadow-primary/10 hover:bg-primary-container transition-all">{{ t('Reset Simulation') }}</button>
                </div>
            </div>
        </header>

        <!-- AI Chat Panel -->
        <div v-if="showAIChat" class="mb-8 bg-gradient-to-r from-primary/10 to-secondary/10 p-6 rounded-xl border border-primary/20">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-primary">smart_toy</span>
                <h3 class="font-bold text-primary">{{ t('AI Assistant Simulator') }}</h3>
            </div>

            <!-- Chat Messages -->
            <div class="bg-white rounded-lg p-4 mb-4 max-h-48 overflow-y-auto space-y-3">
                <div v-if="!aiMessages || aiMessages.length === 0" class="text-center text-gray-500 py-4">
                    {{ t('Ceritakan situasi bisnis Anda untuk memulai simulasi') }}
                </div>
                <div v-for="(msg, idx) in (aiMessages || [])" :key="idx" v-if="msg && msg.role && msg.content" :class="[
                    'flex gap-2',
                    msg.role === 'user' ? 'justify-end' : 'justify-start'
                ]">
                    <div :class="[
                        'max-w-xs px-4 py-2 rounded-lg text-sm',
                        msg.role === 'user' 
                            ? 'bg-primary text-white' 
                            : 'bg-gray-100 text-gray-900'
                    ]">
                        {{ msg.content }}
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="space-y-3">
                <textarea
                    v-model="aiInput"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    rows="3"
                    placeholder="Contoh: Saya pedagang bakso, harga daging naik 15%, biaya operasional Rp 5 juta..."
                ></textarea>
                <div class="flex gap-2">
                    <button
                        @click="extractStoryAndSimulate"
                        :disabled="aiLoading || !aiInput.trim()"
                        class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark disabled:bg-gray-400 transition-colors font-medium flex items-center justify-center gap-2"
                    >
                        <span v-if="aiLoading" class="animate-spin">⌛</span>
                        {{ t('Analisis & Simulasi') }}
                    </button>
                    <button
                        @click="sendAIMessage"
                        :disabled="aiLoading || !aiInput.trim()"
                        class="flex-1 px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary-dark disabled:bg-gray-400 transition-colors font-medium flex items-center justify-center gap-2"
                    >
                        <span v-if="aiLoading" class="animate-spin">⌛</span>
                        {{ t('Chat Konsultasi') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Simulation Panel -->
        <div v-if="showAdvancedPanel" class="mb-8 bg-gradient-to-r from-secondary/10 to-primary/10 p-6 rounded-xl border border-secondary/20">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-secondary-dark">trending_up</span>
                <h3 class="font-bold text-secondary-dark">{{ t('Advanced Weighted Regression Simulator') }}</h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <!-- Commodity Selector -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ t('Komoditas') }}</label>
                    <select v-model.number="selectedCommodity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="">-- {{ t('Pilih') }} --</option>
                        <option v-for="commodity in all_commodities" :key="commodity.id" :value="commodity.id">
                            {{ commodity.name }} ({{ commodity.unit }})
                        </option>
                    </select>
                </div>

                <!-- Base Price -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ t('Harga Dasar') }}</label>
                    <input v-model.number="advancedForm.base_price" type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="Rp" />
                </div>

                <!-- Simulated Price -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ t('Harga Simulasi') }}</label>
                    <input v-model.number="advancedForm.simulated_price" type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="Rp" />
                </div>

                <!-- Base Operational Cost -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ t('Biaya Operasional') }}</label>
                    <input v-model.number="advancedForm.base_operational_cost" type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="Rp/bulan" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- Weight Slider -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        {{ t('Bobot Komoditas') }}: {{ (advancedForm.weight_in_basket * 100).toFixed(0) }}%
                    </label>
                    <input v-model.number="advancedForm.weight_in_basket" type="range" min="0" max="1" step="0.1" class="w-full" />
                </div>

                <!-- Seasonal Factor -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ t('Faktor Musiman') }}</label>
                    <select v-model.number="advancedForm.seasonal_factor" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                        <option value="0.8">{{ t('Musim Sepi') }} (0.8x)</option>
                        <option value="1.0">{{ t('Normal') }} (1.0x)</option>
                        <option value="1.2">{{ t('Musim Ramai') }} (1.2x)</option>
                        <option value="1.3">{{ t('Musim Panen') }} (1.3x)</option>
                    </select>
                </div>
            </div>

            <button
                @click="runAdvancedSimulation"
                class="w-full px-4 py-3 bg-primary text-white rounded-lg hover:bg-secondary-dark transition-colors font-bold flex items-center justify-center gap-2"
            >
                <span class="material-symbols-outlined">calculate</span>
                {{ t('Jalankan Weighted Regression') }}
            </button>
        </div>

        <!-- Advanced Results Panel -->
        <div v-if="advancedCalculationResult" class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-green-600">verified</span>
                    <h3 class="font-bold text-green-900">{{ t('Hasil Simulasi') }}</h3>
                </div>
                <div :class="['px-3 py-1 rounded-full font-bold text-white', getRiskColor(advancedCalculationResult.impact_score)]">
                    {{ advancedCalculationResult.risk_level }}
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg">
                    <p class="text-xs text-gray-600 font-bold">{{ t('Harga Dasar') }}</p>
                    <p class="text-xl font-bold">Rp {{ formatPrice(advancedCalculationResult.price_analysis.base_price) }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <p class="text-xs text-gray-600 font-bold">{{ t('Harga Simulasi') }}</p>
                    <p class="text-xl font-bold">Rp {{ formatPrice(advancedCalculationResult.price_analysis.simulated_price) }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <p class="text-xs text-gray-600 font-bold">{{ t('Perubahan Harga') }}</p>
                    <p :class="['text-xl font-bold', advancedCalculationResult.price_analysis.price_change_percent > 0 ? 'text-red-600' : 'text-green-600']">
                        {{ advancedCalculationResult.price_analysis.price_change_percent }}%
                    </p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <p class="text-xs text-gray-600 font-bold">{{ t('Impact Score') }}</p>
                    <p class="text-xl font-bold text-primary">{{ advancedCalculationResult.impact_score }}/100</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Recommendations -->
                <div class="bg-white p-4 rounded-lg">
                    <h4 class="font-bold text-gray-900 mb-3">{{ t('Rekomendasi') }}</h4>
                    <ul class="space-y-2">
                        <li v-for="(rec, idx) in advancedCalculationResult.recommendations" :key="idx" class="text-sm text-gray-700">
                            <span class="font-bold">{{ rec.type }}:</span> {{ rec.text }}
                        </li>
                    </ul>
                </div>

                <!-- Weighted Impact -->
                <div class="bg-white p-4 rounded-lg">
                    <h4 class="font-bold text-gray-900 mb-3">{{ t('Dampak Operasional') }}</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>{{ t('Peningkatan Biaya') }}</span>
                            <span class="font-bold">Rp {{ formatPrice(advancedCalculationResult.weighted_impact.cost_increase_absolute) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ t('Persentase Kenaikan') }}</span>
                            <span class="font-bold">{{ advancedCalculationResult.weighted_impact.cost_increase_percent }}%</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span class="font-bold">{{ t('Biaya Baru') }}</span>
                            <span class="font-bold text-lg">Rp {{ formatPrice(advancedCalculationResult.weighted_impact.new_operational_cost) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Recommendations -->
            <div v-if="supplierRecommendations.length > 0" class="bg-white p-4 rounded-lg">
                <h4 class="font-bold text-gray-900 mb-4">🏪 {{ t('Rekomendasi Supplier') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div v-for="supplier in supplierRecommendations.slice(0, 3)" :key="supplier.supplier_id" class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
                        <h5 class="font-bold text-gray-900 text-sm mb-2">{{ supplier.nama_supplier }}</h5>
                        <p class="text-xs text-gray-600 mb-3">📍 {{ supplier.lokasi }}</p>
                        <div class="space-y-1 mb-3 text-sm">
                            <div class="flex justify-between">
                                <span>{{ t('Harga') }}</span>
                                <span class="font-bold">Rp {{ formatPrice(supplier.price) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>{{ t('Hemat') }}</span>
                                <span :class="['font-bold', supplier.is_cheaper ? 'text-green-600' : 'text-red-600']">
                                    {{ supplier.savings_percent }}%
                                </span>
                            </div>
                        </div>
                        <button
                            @click="simulateWithSupplier(supplier)"
                            class="w-full px-3 py-2 bg-primary text-white text-xs rounded font-bold hover:bg-primary-dark transition-colors"
                        >
                            {{ t('Simulasi Harga Ini') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <section class="col-span-12 lg:col-span-4 flex flex-col gap-6">
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_50px_rgba(0,33,22,0.04)] h-full">
                    <div class="flex items-center gap-2 mb-8 text-primary">
                        <span class="material-symbols-outlined">tune</span>
                        <h3 class="font-headline font-bold text-lg">{{ t('What-If Variables') }}</h3>
                    </div>

                    <div class="space-y-10">
                        <div v-for="(data, name) in db_commodities" :key="name" class="space-y-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <label class="font-headline font-semibold text-on-surface block">{{ name }}</label>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                        {{ t('Market') }}: Rp {{ formatPrice(data.price) }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span :class="['font-bold px-3 py-1 rounded-full text-sm block mb-1', sliders[name] > 50 ? 'text-error bg-error/5' : 'text-primary bg-primary/5']">
                                        {{ calculateChange(sliders[name]) }}
                                    </span>
                                    <span class="text-[10px] font-bold text-primary">
                                        {{ t('Sim') }}: Rp {{ formatPrice((data.price * sliders[name]) / 50) }}
                                    </span>
                                </div>
                            </div>
                            <input v-model="sliders[name]" type="range" min="0" max="100" class="w-full h-1.5 bg-surface-container-high rounded-full appearance-none cursor-pointer accent-primary" />
                            <div class="flex justify-between text-[10px] text-on-surface-variant font-medium uppercase tracking-widest">
                                <span>{{ t('Deflation') }}</span>
                                <span>{{ t('Critical Spike') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-slate-100">
                        <div class="flex items-center gap-3 p-4 bg-secondary-fixed/30 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">info</span>
                            <p class="text-xs text-on-secondary-fixed-variant leading-relaxed">
                                {{ t('Connected to') }} <strong>{{ t('Live Database') }}</strong>. {{ t('Using weighted regression for') }} {{ Object.keys(db_commodities).length }} {{ t('active commodities') }}.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="col-span-12 lg:col-span-8 grid grid-cols-2 gap-6">
                <article class="col-span-2 bg-gradient-to-br from-primary to-primary-container p-8 rounded-xl text-white shadow-xl flex flex-col justify-between min-h-[240px]">
                    <div>
                        <span class="text-primary-fixed uppercase tracking-widest font-bold text-xs">{{ t('Simulated Inflation Index') }}</span>
                        <h3 class="text-7xl font-headline font-extrabold mt-4">{{ simulatedIndex }}%</h3>
                        <p class="text-primary-fixed/80 mt-2 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">trending_up</span>
                            {{ (simulatedIndex - baseline_inflation).toFixed(2) }}% {{ t('from current baseline') }}
                        </p>
                    </div>
                    <div class="flex gap-8 mt-8 border-t border-white/10 pt-6">
                        <div>
                            <p class="text-primary-fixed/60 text-xs font-semibold uppercase">{{ t('SME Operational Cost') }}</p>
                            <p class="text-xl font-headline font-bold">Rp 4.2M <span class="text-xs font-normal opacity-70">/ {{ t('avg. unit') }}</span></p>
                        </div>
                        <div>
                            <p class="text-primary-fixed/60 text-xs font-semibold uppercase">{{ t('Market Confidence') }}</p>
                            <p class="text-xl font-headline font-bold text-secondary-fixed-dim">
                                {{ simulatedIndex > 5 ? t('Vulnerable') : t('Moderate') }}
                            </p>
                        </div>
                    </div>
                </article>

                <section class="col-span-2 lg:col-span-1 bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_50px_rgba(0,33,22,0.04)]">
                    <div class="flex items-center justify-between mb-8">
                        <h4 class="font-headline font-bold text-on-surface">{{ t('Purchasing Power Decay') }}</h4>
                        <span class="material-symbols-outlined text-slate-400">more_horiz</span>
                    </div>
                    <div class="aspect-video relative flex items-end gap-2 mb-4">
                        <div v-for="(h, i) in [90, 85, 70, 55, 40, 30]" :key="i" 
                             :style="{ height: h + '%' }" 
                             :class="['flex-1 rounded-t-md transition-all', i < 3 ? 'bg-surface-container-high' : 'bg-primary/60']">
                        </div>
                    </div>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        {{ t('Predicted decline in household discretionary spending based on current simulation.') }}
                    </p>
                </section>

                <section class="col-span-2 lg:col-span-1 bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_50px_rgba(0,33,22,0.04)]">
                    <div class="flex items-center justify-between mb-8">
                        <h4 class="font-headline font-bold text-on-surface">{{ t('Sectoral Vulnerability') }}</h4>
                        <span class="px-2 py-0.5 bg-secondary-fixed text-on-secondary-fixed font-bold text-[10px] rounded uppercase">{{ t('Live Data') }}</span>
                    </div>
                    <div class="space-y-6">
                        <div v-for="sector in [
                            { name: t('F&B Sector'), risk: simulatedIndex > 5 ? t('High Risk') : 'Medium', val: simulatedIndex * 15, color: 'bg-error', icon: 'restaurant' },
                            { name: t('Logistics'), risk: t('Stable'), val: 35, color: 'bg-primary', icon: 'local_shipping' }
                        ]" :key="sector.name" class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-lg bg-secondary/10 flex items-center justify-center text-secondary">
                                <span class="material-symbols-outlined">{{ sector.icon }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-bold">{{ sector.name }}</span>
                                    <span :class="['font-bold', sector.val > 70 ? 'text-error' : 'text-primary']">{{ sector.risk }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-surface-container rounded-full">
                                    <div :class="['h-full rounded-full transition-all duration-500', sector.color]" :style="{ width: sector.val + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <button @click="showSaveModal = true" class="fixed bottom-8 right-8 h-14 px-6 rounded-full bg-primary text-white shadow-[0_20px_50px_rgba(0,33,22,0.2)] flex items-center gap-3 font-headline font-bold hover:scale-105 active:scale-95 transition-all">
            <span class="material-symbols-outlined">save</span>
            {{ t('Save Scenario') }}
        </button>

        <ExportReportModal :isOpen="showExportModal" @close="showExportModal = false" />
        <SaveScenarioModal :isOpen="showSaveModal" @close="showSaveModal = false" @save="(scenario) => { console.log('Scenario saved:', scenario); showSaveModal = false; }" />
    </StitchLayout>
</template>