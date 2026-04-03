<template>
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">
      📊 {{ $t('Hasil Simulasi') || 'Hasil Simulasi' }}
    </h2>

    <div v-if="loading" class="flex items-center justify-center py-8">
      <div class="animate-spin mr-3">⌛</div>
      <p>{{ $t('Menganalisis simulasi...') || 'Menganalisis...' }}</p>
    </div>

    <div v-else class="space-y-6">
      <!-- Summary Cards -->
      <div class="grid grid-cols-2 gap-4">
        <!-- Impact Score Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
          <p class="text-sm text-gray-700 font-medium">{{ $t('Skor Dampak') || 'Skor Dampak' }}</p>
          <p class="text-3xl font-bold text-blue-600">{{ result.impact_score }}/100</p>
          <p class="text-xs text-gray-600 mt-1">{{ riskLevelLabel }}</p>
        </div>

        <!-- Risk Level Card -->
        <div :class="[
          'rounded-lg p-4',
          riskLevelBgClass
        ]">
          <p class="text-sm font-medium" :class="riskLevelTextClass">
            {{ $t('Level Risiko') || 'Level Risiko' }}
          </p>
          <p class="text-2xl font-bold mt-2" :class="riskLevelTextClass">
            {{ result.risk_level }}
          </p>
        </div>
      </div>

      <!-- Price Analysis -->
      <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-900 mb-3">{{ $t('Analisis Harga') || 'Analisis Harga' }}</h3>
        <dl class="space-y-2 text-sm">
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Komoditas') || 'Komoditas' }}</dt>
            <dd class="font-semibold text-gray-900">{{ result.commodity.name }} ({{ result.commodity.unit }})</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Harga Saat Ini') || 'Harga Saat Ini' }}</dt>
            <dd class="font-semibold text-gray-900">Rp {{ formatPrice(result.price_analysis.base_price) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Harga Simulasi') || 'Harga Simulasi' }}</dt>
            <dd class="font-semibold text-gray-900">Rp {{ formatPrice(result.price_analysis.simulated_price) }}</dd>
          </div>
          <div class="flex justify-between pt-2 border-t border-gray-200">
            <dt class="text-gray-600">{{ $t('Perubahan') || 'Perubahan' }}</dt>
            <dd :class="priceChangeBadgeClass">
              Rp {{ formatPrice(result.price_analysis.price_change) }}
              ({{ result.price_analysis.price_change_percent }}%)
            </dd>
          </div>
        </dl>
      </div>

      <!-- Weighted Impact -->
      <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-900 mb-3">{{ $t('Dampak Tertimbang') || 'Dampak Tertimbang' }}</h3>
        <dl class="space-y-2 text-sm">
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Bobot Komoditas') || 'Bobot Komoditas' }}</dt>
            <dd class="font-semibold text-gray-900">{{ (result.weighted_impact.weight_in_basket * 100).toFixed(0) }}%</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Faktor Musiman') || 'Faktor Musiman' }}</dt>
            <dd class="font-semibold text-gray-900">{{ result.weighted_impact.seasonal_factor.toFixed(2) }}x</dd>
          </div>
          <div class="flex justify-between pt-2 border-t border-gray-200">
            <dt class="text-gray-600">{{ $t('Peningkatan Biaya (Absolut)') || 'Peningkatan Biaya' }}</dt>
            <dd class="font-semibold" :class="{
              'text-red-600': result.weighted_impact.cost_increase_absolute > 0,
              'text-green-600': result.weighted_impact.cost_increase_absolute < 0,
            }">
              Rp {{ formatPrice(result.weighted_impact.cost_increase_absolute) }}
            </dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Peningkatan Biaya (%)') || 'Peningkatan Biaya (%)' }}</dt>
            <dd class="font-semibold" :class="{
              'text-red-600': result.weighted_impact.cost_increase_percent > 0,
              'text-green-600': result.weighted_impact.cost_increase_percent < 0,
            }">
              {{ result.weighted_impact.cost_increase_percent.toFixed(2) }}%
            </dd>
          </div>
          <div class="flex justify-between pt-2 border-t border-gray-200">
            <dt class="text-gray-600">{{ $t('Biaya Operasional Baru') || 'Biaya Operasional Baru' }}</dt>
            <dd class="font-semibold text-gray-900">Rp {{ formatPrice(result.weighted_impact.new_operational_cost) }}</dd>
          </div>
        </dl>
      </div>

      <!-- Recommendations -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-900 mb-3">{{ $t('Rekomendasi') || 'Rekomendasi' }}</h3>
        <ul class="space-y-3">
          <li v-for="(rec, idx) in result.recommendations" :key="idx" class="flex gap-3">
            <span class="text-lg">{{ getRecommendationIcon(rec.type) }}</span>
            <p class="text-sm text-gray-700">{{ rec.text }}</p>
          </li>
        </ul>
      </div>

      <!-- Supplier Used Info -->
      <div v-if="supplierUsed" class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="font-semibold text-green-900 mb-3">{{ $t('Menggunakan Supplier') || 'Menggunakan Supplier' }}</h3>
        <dl class="space-y-2 text-sm">
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Nama Supplier') || 'Nama Supplier' }}</dt>
            <dd class="font-semibold text-gray-900">{{ supplierUsed.nama }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Lokasi') || 'Lokasi' }}</dt>
            <dd class="text-gray-900">{{ supplierUsed.lokasi }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-600">{{ $t('Harga dari Supplier') || 'Harga dari Supplier' }}</dt>
            <dd class="font-semibold text-green-600">Rp {{ formatPrice(supplierUsed.price) }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  result: {
    type: Object,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price || 0)
}

const riskLevelLabel = computed(() => {
  const labels = {
    'LOW': '🟢 Rendah',
    'MEDIUM': '🟡 Sedang',
    'HIGH': '🟠 Tinggi',
    'CRITICAL': '🔴 Kritis',
  }
  return labels[props.result.risk_level] || props.result.risk_level
})

const riskLevelBgClass = computed(() => {
  const classes = {
    'LOW': 'bg-green-50',
    'MEDIUM': 'bg-yellow-50',
    'HIGH': 'bg-orange-50',
    'CRITICAL': 'bg-red-50',
  }
  return classes[props.result.risk_level] || 'bg-gray-50'
})

const riskLevelTextClass = computed(() => {
  const classes = {
    'LOW': 'text-green-700',
    'MEDIUM': 'text-yellow-700',
    'HIGH': 'text-orange-700',
    'CRITICAL': 'text-red-700',
  }
  return classes[props.result.risk_level] || 'text-gray-700'
})

const priceChangeBadgeClass = computed(() => {
  const change = props.result.price_analysis.price_change_percent
  if (change > 0) return 'text-red-600 font-semibold'
  if (change < 0) return 'text-green-600 font-semibold'
  return 'text-gray-600'
})

const getRecommendationIcon = (type) => {
  const icons = {
    'urgent': '⚠️',
    'mitigation': '🛡️',
    'opportunity': '🎯',
    'caution': '⏰',
    'analytics': '📈',
  }
  return icons[type] || '💡'
}

const supplierUsed = computed(() => {
  return props.result.supplier_used || null
})
</script>
