<script setup>
import { ref, computed, onMounted } from 'vue';
import StitchLayout from '@/Layouts/StitchLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();
const page = usePage();

// State
const suppliers = ref([]);
const commodities = ref([]);
const loading = ref(false);
const selectedCommodity = ref(null);
const simulatedPrice = ref(0);
const userLat = ref(null);
const userLong = ref(null);
const searchLimit = ref(5);
const sortBy = ref('distance');
const filterType = ref('all');

// Fetch commodities on mount
onMounted(async () => {
    await fetchCommodities();
});

// Fetch commodities list
const fetchCommodities = async () => {
    try {
        const response = await fetch('/api/commodities', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            commodities.value = data.data || [];
            console.log('Commodities loaded:', commodities.value);
        } else {
            console.error('Error response:', response.status);
        }
    } catch (error) {
        console.error('Error fetching commodities:', error);
    }
};

// Search suppliers
const searchSuppliers = async () => {
    if (!selectedCommodity.value) {
        alert(t('Silakan pilih komoditas terlebih dahulu'));
        return;
    }

    if (simulatedPrice.value <= 0) {
        alert(t('Harga simulasi harus lebih besar dari 0'));
        return;
    }

    loading.value = true;
    suppliers.value = [];

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const params = new URLSearchParams({
            commodity_id: selectedCommodity.value,
            simulated_price: simulatedPrice.value,
            limit: searchLimit.value,
        });

        if (userLat.value && userLong.value) {
            params.append('user_lat', userLat.value);
            params.append('user_long', userLong.value);
        }

        const response = await fetch(`/simulation/suppliers?${params}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            suppliers.value = data.data || [];
            if (suppliers.value.length === 0) {
                alert(t('Tidak ada supplier yang ditemukan untuk kriteria ini'));
            }
        } else {
            alert(data.message || t('Error memuat supplier'));
        }
    } catch (error) {
        console.error('Error searching suppliers:', error);
        alert(t('Terjadi kesalahan saat mencari supplier'));
    } finally {
        loading.value = false;
    }
};

// Get user location
const getLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLat.value = position.coords.latitude;
                userLong.value = position.coords.longitude;
                alert(t('Lokasi berhasil diambil'));
            },
            (error) => {
                console.error('Error getting location:', error);
                alert(t('Tidak dapat mengambil lokasi'));
            }
        );
    } else {
        alert(t('Geolocation tidak didukung di browser ini'));
    }
};

// Filtered suppliers
const filteredSuppliers = computed(() => {
    let result = suppliers.value;

    // Filter by type
    if (filterType.value !== 'all') {
        result = result.filter(s => s.type === filterType.value);
    }

    // Sort
    if (sortBy.value === 'distance' && userLat.value && userLong.value) {
        result = result.sort((a, b) => (a.distance || 0) - (b.distance || 0));
    } else if (sortBy.value === 'price') {
        result = result.sort((a, b) => (a.price || 0) - (b.price || 0));
    } else if (sortBy.value === 'rating') {
        result = result.sort((a, b) => (b.rating || 0) - (a.rating || 0));
    }

    return result;
});

// Simulate with supplier
const simulateWithSupplier = async (supplier) => {
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch('/simulation/simulate-with-supplier', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                commodity_id: selectedCommodity.value,
                supplier_id: supplier.id,
                supplier_price: supplier.price,
                quantity: 1,
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            console.log('Simulation result:', data);
            alert(`✓ ${supplier.name}\n${t('Simulasi berhasil')}!`);
            // Store simulation result in session/local storage if needed
            sessionStorage.setItem('lastSimulation', JSON.stringify(data.data));
            // Navigate to simulation page
            window.location.href = '/simulation';
        } else {
            alert(data.message || t('Error simulasi'));
        }
    } catch (error) {
        console.error('Error simulating with supplier:', error);
        alert(`${t('Terjadi kesalahan')}: ${error.message}`);
    }
};

// Get commodity name
const getCommodityName = (id) => {
    const commodity = commodities.value.find(c => c.id === id);
    return commodity?.name || `Commodity ${id}`;
};

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Get distance label
const getDistanceLabel = (distance) => {
    if (!distance) return 'N/A';
    if (distance < 1) return `${(distance * 1000).toFixed(0)} m`;
    return `${distance.toFixed(2)} km`;
};
</script>

<template>
    <Head title="Supplier" />

    <StitchLayout>
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">{{ t('Supplier') }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ t('Temukan supplier terbaik untuk komoditas Anda') }}</p>
            </div>
            <a href="/simulation" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                ← {{ t('Kembali ke Simulasi') }}
            </a>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Search Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-20">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('Filter Supplier') }}</h3>

                    <!-- Commodity Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('Komoditas') }} <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="selectedCommodity"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option :value="null">{{ t('Pilih komoditas...') }}</option>
                            <option v-for="commodity in commodities" :key="commodity.id" :value="commodity.id">
                                {{ commodity.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Price Input -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('Harga Simulasi (IDR)') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model.number="simulatedPrice"
                            type="number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            :placeholder="t('Masukkan harga')"
                        />
                    </div>

                    <!-- Location -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('Lokasi') }}</label>
                        <button
                            @click="getLocation"
                            class="w-full px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium"
                        >
                            📍 {{ userLat && userLong ? t('✓ Lokasi diambil') : t('Ambil Lokasi Saat Ini') }}
                        </button>
                    </div>

                    <!-- Search Limit -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('Jumlah Hasil') }}: {{ searchLimit }}
                        </label>
                        <input
                            v-model.number="searchLimit"
                            type="range"
                            min="1"
                            max="20"
                            class="w-full"
                        />
                    </div>

                    <!-- Filter Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('Tipe Supplier') }}</label>
                        <select
                            v-model="filterType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="all">{{ t('Semua') }}</option>
                            <option value="wholesaler">{{ t('Wholesaler') }}</option>
                            <option value="retailer">{{ t('Retailer') }}</option>
                            <option value="distributor">{{ t('Distributor') }}</option>
                            <option value="producer">{{ t('Producer') }}</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('Urutkan') }}</label>
                        <select
                            v-model="sortBy"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="distance">{{ t('Jarak Terdekat') }}</option>
                            <option value="price">{{ t('Harga Terendah') }}</option>
                            <option value="rating">{{ t('Rating Tertinggi') }}</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <button
                        @click="searchSuppliers"
                        :disabled="loading"
                        class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:bg-gray-400 transition-colors font-medium"
                    >
                        <span v-if="!loading">🔍 {{ t('Cari Supplier') }}</span>
                        <span v-else>{{ t('Sedang mencari...') }}</span>
                    </button>
                </div>
            </div>

            <!-- Results Panel -->
            <div class="lg:col-span-3">
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <div class="text-2xl font-bold text-emerald-600">{{ suppliers.length }}</div>
                        <div class="text-sm text-gray-600">{{ t('Total Supplier') }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ suppliers.length > 0 ? formatCurrency(Math.min(...suppliers.map(s => s.price || 0))) : '-' }}
                        </div>
                        <div class="text-sm text-gray-600">{{ t('Harga Terendah') }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ suppliers.length > 0 ? (suppliers.reduce((sum, s) => sum + (s.rating || 0), 0) / suppliers.length).toFixed(1) : '-' }}
                        </div>
                        <div class="text-sm text-gray-600">{{ t('Rating Rata-rata') }}</div>
                    </div>
                </div>

                <!-- Supplier Cards -->
                <div v-if="filteredSuppliers.length > 0" class="space-y-4">
                    <div v-for="supplier in filteredSuppliers" :key="supplier.id" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">{{ supplier.name }}</h4>
                                <p class="text-sm text-gray-500">{{ supplier.type || '-' }} | {{ supplier.address || 'N/A' }}</p>
                            </div>
                            <button
                                @click="simulateWithSupplier(supplier)"
                                class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-sm font-medium"
                            >
                                🔄 {{ t('Simulasi') }}
                            </button>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Price -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ t('Harga') }}</p>
                                <p class="text-lg font-bold text-green-600">{{ formatCurrency(supplier.price || 0) }}</p>
                            </div>

                            <!-- Rating -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ t('Rating') }}</p>
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-yellow-500">{{ supplier.rating || 'N/A' }}</span>
                                    <span class="text-sm text-gray-500 ml-1">⭐</span>
                                </div>
                            </div>

                            <!-- Distance -->
                            <div v-if="userLat && userLong">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ t('Jarak') }}</p>
                                <p class="text-lg font-bold text-blue-600">{{ getDistanceLabel(supplier.distance) }}</p>
                            </div>

                            <!-- Stock -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">{{ t('Stok') }}</p>
                                <p class="text-lg font-bold" :class="supplier.stock > 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ supplier.stock }} {{ supplier.unit || t('unit') }}
                                </p>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-700">
                                <strong>{{ t('Kontak') }}:</strong> {{ supplier.phone || 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-700">
                                <strong>Email:</strong> {{ supplier.email || 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ t('Belum ada hasil pencarian') }}</h3>
                    <p class="text-gray-600">{{ t('Gunakan filter di sebelah kiri untuk mencari supplier') }}</p>
                </div>
            </div>
        </div>
    </StitchLayout>
</template>

<style scoped>
/* Custom styles if needed */
</style>
