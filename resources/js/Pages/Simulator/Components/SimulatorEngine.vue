<template>
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">
      ⚙️ {{ $t('Parameter Simulasi') || 'Parameter Simulasi' }}
    </h2>

    <form @submit.prevent="submitSimulation" class="space-y-4">
      <!-- Commodity Selection -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('Komoditas') || 'Komoditas' }}
        </label>
        <select
          v-model.number="form.commodity_id"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="">-- {{ $t('Pilih Komoditas') || 'Pilih Komoditas' }} --</option>
          <option v-for="commodity in commodities" :key="commodity.id" :value="commodity.id">
            {{ commodity.name }} ({{ commodity.unit }}) - Rp {{ formatPrice(commodity.price) }}
          </option>
        </select>
      </div>

      <!-- Base Price -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('Harga Dasar') || 'Harga Dasar' }} (Rp)
        </label>
        <input
          v-model.number="form.base_price"
          type="number"
          step="0.01"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          placeholder="0"
        />
      </div>

      <!-- Simulated Price -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('Harga Simulasi') || 'Harga Simulasi' }} (Rp)
        </label>
        <input
          v-model.number="form.simulated_price"
          type="number"
          step="0.01"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          placeholder="0"
        />
        <p class="text-xs text-gray-500 mt-1">
          {{ $t('Perubahan') || 'Perubahan' }}: 
          <span :class="priceChangeBadgeClass">
            {{ priceChangePercent }}%
          </span>
        </p>
      </div>

      <!-- Weight in Basket -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('Bobot Komoditas') || 'Bobot Komoditas' }}: {{ (form.weight_in_basket * 100).toFixed(0) }}%
        </label>
        <input
          v-model.number="form.weight_in_basket"
          type="range"
          min="0"
          max="1"
          step="0.1"
          class="w-full"
        />
        <p class="text-xs text-gray-500 mt-1">
          {{ $t('Proporsi komoditas dalam keranjang input') || '0 = tidak ada, 1 = 100% input' }}
        </p>
      </div>

      <!-- Seasonal Factor -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('Faktor Musiman') || 'Faktor Musiman' }}: {{ form.seasonal_factor.toFixed(2) }}x
        </label>
        <select
          v-model.number="form.seasonal_factor"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="0.8">{{ $t('Musim Sepi') || 'Musim Sepi' }} (0.8x)</option>
          <option value="1.0">{{ $t('Normal') || 'Normal' }} (1.0x)</option>
          <option value="1.2">{{ $t('Musim Ramai') || 'Musim Ramai' }} (1.2x)</option>
          <option value="1.3">{{ $t('Musim Panen') || 'Musim Panen' }} (1.3x)</option>
        </select>
      </div>

      <!-- Base Operational Cost -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('Biaya Operasional Bulanan') || 'Biaya Operasional Bulanan' }} (Rp)
        </label>
        <input
          v-model.number="form.base_operational_cost"
          type="number"
          step="0.01"
          min="0"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          placeholder="0"
        />
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="!isFormValid"
        class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:bg-gray-400 transition-colors font-semibold"
      >
        {{ $t('Jalankan Simulasi') || 'Jalankan Simulasi' }}
      </button>
    </form>

    <!-- Quick Templates -->
    <div class="mt-6 pt-6 border-t border-gray-200">
      <h3 class="text-sm font-medium text-gray-700 mb-3">{{ $t('Template Cepat') || 'Template Cepat' }}</h3>
      <div class="space-y-2">
        <button
          @click="loadTemplate('bakery')"
          class="w-full text-left px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded text-sm text-gray-700"
        >
          🍞 {{ $t('Toko Roti') || 'Toko Roti' }}
        </button>
        <button
          @click="loadTemplate('warung')"
          class="w-full text-left px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded text-sm text-gray-700"
        >
          🍲 {{ $t('Warung Makan') || 'Warung Makan' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

defineProps({
  commodities: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits(['simulation-requested'])

const form = ref({
  commodity_id: '',
  base_price: '',
  simulated_price: '',
  weight_in_basket: 0.5,
  seasonal_factor: 1.0,
  base_operational_cost: '',
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price || 0)
}

const priceChangePercent = computed(() => {
  if (!form.value.base_price || !form.value.simulated_price) return '0'
  const change = ((form.value.simulated_price - form.value.base_price) / form.value.base_price) * 100
  return change.toFixed(2)
})

const priceChangeBadgeClass = computed(() => {
  const change = parseFloat(priceChangePercent.value)
  if (change > 0) return 'text-red-600 font-semibold'
  if (change < 0) return 'text-green-600 font-semibold'
  return 'text-gray-600'
})

const isFormValid = computed(() => {
  return (
    form.value.commodity_id &&
    form.value.base_price > 0 &&
    form.value.simulated_price >= 0 &&
    form.value.base_operational_cost > 0
  )
})

const submitSimulation = () => {
  emit('simulation-requested', form.value)
}

const loadTemplate = (type) => {
  if (type === 'bakery') {
    form.value = {
      commodity_id: '',
      base_price: 10000,
      simulated_price: 11500,
      weight_in_basket: 0.4,
      seasonal_factor: 1.0,
      base_operational_cost: 5000000,
    }
  } else if (type === 'warung') {
    form.value = {
      commodity_id: '',
      base_price: 15000,
      simulated_price: 17500,
      weight_in_basket: 0.6,
      seasonal_factor: 1.1,
      base_operational_cost: 8000000,
    }
  }
}
</script>
