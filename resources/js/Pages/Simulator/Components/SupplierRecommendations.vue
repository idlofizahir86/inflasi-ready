<template>
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">
      🏪 {{ $t('Rekomendasi Supplier') || 'Rekomendasi Supplier' }}
    </h2>

    <div v-if="loading" class="flex items-center justify-center py-8">
      <div class="animate-spin mr-3">⌛</div>
      <p>{{ $t('Mencari supplier...') || 'Mencari supplier...' }}</p>
    </div>

    <div v-else-if="suppliers.length === 0" class="text-center py-8 text-gray-500">
      <p>{{ $t('Tidak ada supplier yang cocok') || 'Tidak ada supplier yang cocok' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div 
        v-for="(supplier, idx) in suppliers"
        :key="supplier.supplier_id"
        class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
      >
        <!-- Header -->
        <div class="flex justify-between items-start mb-3">
          <div>
            <h3 class="font-semibold text-gray-900">{{ idx + 1 }}. {{ supplier.nama_supplier }}</h3>
            <p class="text-sm text-gray-500">📍 {{ supplier.lokasi }}</p>
          </div>
          <div class="text-right">
            <div v-if="supplier.rating" class="text-sm font-semibold text-yellow-500">
              ⭐ {{ supplier.rating }}/5
            </div>
          </div>
        </div>

        <!-- Price Comparison -->
        <div class="grid grid-cols-3 gap-4 py-3 border-y border-gray-100 mb-3">
          <div>
            <p class="text-xs text-gray-500">{{ $t('Simulasi') || 'Simulasi' }}</p>
            <p class="font-semibold text-gray-900">Rp {{ formatPrice(supplier.simulated_price) }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500">{{ $t('Supplier') || 'Supplier' }}</p>
            <p class="font-semibold text-gray-900">Rp {{ formatPrice(supplier.price) }}</p>
          </div>
          <div :class="[
            'text-right',
            supplier.is_cheaper ? 'text-green-600' : 'text-red-600'
          ]">
            <p class="text-xs">{{ supplier.is_cheaper ? 'HEMAT' : 'MAHAL' }}</p>
            <p class="font-semibold">{{ supplier.savings_percent }}%</p>
          </div>
        </div>

        <!-- Details -->
        <div class="space-y-2 text-sm mb-4">
          <div class="flex justify-between">
            <span class="text-gray-600">{{ $t('Minimum Order') || 'Minimum Order' }}</span>
            <span class="font-medium">{{ supplier.min_order_kg }} kg</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">{{ $t('Frekuensi Update') || 'Frekuensi Update' }}</span>
            <span class="font-medium">
              {{ supplier.price_update_frequency === 'daily' ? '🔄 Harian' : '📅 Mingguan' }}
            </span>
          </div>
          <div v-if="supplier.distance_km" class="flex justify-between">
            <span class="text-gray-600">{{ $t('Jarak') || 'Jarak' }}</span>
            <span class="font-medium">{{ supplier.distance_km }} km</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">{{ $t('Harga Diperbarui') || 'Harga Diperbarui' }}</span>
            <span class="font-medium">{{ formatDate(supplier.price_date) }}</span>
          </div>
        </div>

        <!-- Contact & Action -->
        <div class="flex gap-2">
          <a
            v-if="supplier.kontak"
            :href="`https://wa.me/${cleanPhone(supplier.kontak)}`"
            target="_blank"
            class="flex-1 px-3 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors text-center"
          >
            {{ $t('Hubungi') || 'Hubungi' }}
          </a>
          <button
            @click="simulateWithSupplier(supplier)"
            class="flex-1 px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition-colors"
          >
            {{ $t('Simulasi') || 'Simulasi' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Savings Summary -->
    <div v-if="suppliers.length > 0" class="mt-6 pt-6 border-t border-gray-200 bg-green-50 rounded-lg p-4">
      <h3 class="font-semibold text-green-900 mb-2">💰 {{ $t('Potensi Penghematan') || 'Potensi Penghematan' }}</h3>
      <p class="text-sm text-green-700">
        {{ $t('Dengan menggunakan supplier termurah, Anda dapat menghemat harga input hingga') || 'Dengan supplier termurah, hemat hingga' }}
        <span class="font-bold">{{ cheapestSavingsPercent }}%</span>
        ({{ cheapestSupplier.savings_percent }}%)
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  suppliers: {
    type: Array,
    required: true,
  },
  simulationResult: {
    type: Object,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['supplier-selected'])

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price || 0)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const cleanPhone = (phone) => {
  // Remove non-numeric characters
  return phone.replace(/\D/g, '')
}

const cheapestSupplier = computed(() => {
  return props.suppliers.length > 0 ? props.suppliers[0] : null
})

const cheapestSavingsPercent = computed(() => {
  if (!cheapestSupplier.value) return '0'
  return Math.abs(cheapestSupplier.value.savings_percent).toFixed(2)
})

const simulateWithSupplier = (supplier) => {
  emit('supplier-selected', supplier)
}
</script>
