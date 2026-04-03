<template>
  <div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">
      💬 {{ $t('Asisten AI') || 'AI Assistant' }}
    </h2>

    <!-- Chat Messages -->
    <div class="space-y-4 mb-4 h-64 overflow-y-auto bg-gray-50 rounded p-4">
      <div v-if="messages.length === 0" class="text-center text-gray-500 py-8">
        {{ $t('Ceritakan situasi bisnis Anda') || 'Ceritakan cerita Anda...' }}
      </div>

      <div v-for="(msg, idx) in messages" :key="idx" :class="[
        'flex',
        msg.role === 'user' ? 'justify-end' : 'justify-start'
      ]">
        <div :class="[
          'max-w-xs px-4 py-2 rounded-lg',
          msg.role === 'user' 
            ? 'bg-blue-500 text-white' 
            : 'bg-gray-200 text-gray-900'
        ]">
          <p class="text-sm">{{ msg.content }}</p>
          <p class="text-xs opacity-70 mt-1">{{ formatTime(msg.timestamp) }}</p>
        </div>
      </div>
    </div>

    <!-- Story Input Form -->
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('Ceritakan situasi Anda') || 'Cerita Bisnis Anda' }}
        </label>
        <textarea
          v-model="storyInput"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          rows="4"
          placeholder="Contoh: Saya pedagang bakso di Bandung. Harga daging sapi naik 15% bulan ini. Daging adalah input utama kami..."
        ></textarea>
      </div>

      <button
        @click="submitStory"
        :disabled="!storyInput.trim() || loadingExtract"
        class="w-full px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:bg-gray-400 transition-colors"
      >
        <span v-if="loadingExtract" class="inline-block animate-spin mr-2">⌛</span>
        {{ $t('Analisis Cerita') || 'Analisis Cerita' }}
      </button>

      <!-- Extracted Parameters Display -->
      <div v-if="extractedParams" class="bg-green-50 border border-green-200 rounded p-4">
        <h3 class="font-semibold text-green-900 mb-2">{{ $t('Parameter Terdeteksi') || 'Parameter Terdeteksi:' }}</h3>
        <dl class="space-y-2 text-sm">
          <div v-if="extractedParams.commodity_id">
            <dt class="font-medium text-gray-700">{{ $t('Komoditas') || 'Komoditas:' }}</dt>
            <dd class="text-gray-900">{{ getCommodityName(extractedParams.commodity_id) }}</dd>
          </div>
          <div v-if="extractedParams.price_change_percent !== undefined">
            <dt class="font-medium text-gray-700">{{ $t('Perubahan Harga') || 'Perubahan Harga:' }}</dt>
            <dd class="text-gray-900">{{ extractedParams.price_change_percent }}%</dd>
          </div>
          <div v-if="extractedParams.business_type">
            <dt class="font-medium text-gray-700">{{ $t('Jenis Usaha') || 'Jenis Usaha:' }}</dt>
            <dd class="text-gray-900">{{ extractedParams.business_type }}</dd>
          </div>
          <div>
            <dt class="font-medium text-gray-700">{{ $t('Bobot Komoditas') || 'Bobot Komoditas:' }}</dt>
            <dd class="text-gray-900">{{ (extractedParams.weight_in_basket * 100).toFixed(0) }}%</dd>
          </div>
        </dl>

        <button
          @click="submitWithExtractedParams"
          class="mt-3 w-full px-3 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors"
        >
          {{ $t('Simulasikan Sekarang') || 'Simulasikan Sekarang' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const emit = defineEmits(['story-submitted'])
const page = usePage()
const commodities = page.props.commodities || []

const storyInput = ref('')
const messages = ref([])
const loadingExtract = ref(false)
const extractedParams = ref(null)

const getCommodityName = (id) => {
  const commodity = commodities.find(c => c.id === id)
  return commodity?.name || `Komoditas #${id}`
}

const formatTime = (timestamp) => {
  if (!timestamp) return ''
  return new Date(timestamp).toLocaleTimeString()
}

const submitStory = async () => {
  if (!storyInput.value.trim()) return

  // Add user message to chat
  messages.value.push({
    role: 'user',
    content: storyInput.value,
    timestamp: new Date(),
  })

  loadingExtract.value = true

  try {
    const response = await fetch(route('simulator.extract-story'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        story: storyInput.value,
      }),
    })

    const data = await response.json()

    if (data.success) {
      extractedParams.value = data.extracted_parameters.data
      
      // Add AI response
      messages.value.push({
        role: 'assistant',
        content: `Saya telah menganalisis cerita Anda. Terdeteksi: komoditas ${getCommodityName(extractedParams.value.commodity_id)}, perubahan harga ${extractedParams.value.price_change_percent}%, jenis usaha ${extractedParams.value.business_type}.`,
        timestamp: new Date(),
      })
    } else {
      messages.value.push({
        role: 'assistant',
        content: `Maaf, saya tidak bisa memahami cerita Anda dengan sempurna. Coba jelaskan dengan detail tentang komoditas dan perubahan harganya.`,
        timestamp: new Date(),
      })
    }

    storyInput.value = ''
  } catch (error) {
    console.error('Error:', error)
    messages.value.push({
      role: 'assistant',
      content: 'Terjadi kesalahan saat menganalisis cerita Anda.',
      timestamp: new Date(),
    })
  } finally {
    loadingExtract.value = false
  }
}

const submitWithExtractedParams = () => {
  if (!extractedParams.value) return

  emit('story-submitted', {
    commodity_id: extractedParams.value.commodity_id,
    base_price: 0, // Will be filled by user from DB
    simulated_price: 0, // Will be filled based on price change
    weight_in_basket: extractedParams.value.weight_in_basket,
    seasonal_factor: extractedParams.value.seasonal_factor,
    base_operational_cost: 0, // Will be filled by user
  })
}
</script>
