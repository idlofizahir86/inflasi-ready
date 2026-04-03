<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">{{ $t('Simulator Harga') || 'Simulator Harga' }}</h1>
        <p class="mt-2 text-gray-600">{{ $t('Simulasi dampak fluktuasi harga komoditas terhadap bisnis Anda') || 'Simulasi dampak fluktuasi harga komoditas' }}</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Chat Assistant & Input Form -->
        <div class="lg:col-span-1 space-y-6">
          <!-- AI Chat Assistant -->
          <ChatAssistant @story-submitted="handleStorySubmitted" />

          <!-- Manual Input Form -->
          <SimulatorEngine 
            :commodities="commodities"
            @simulation-requested="handleSimulationRequest"
          />
        </div>

        <!-- Right: Results & Supplier Recommendations -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Simulation Results -->
          <SimulationResults 
            v-if="simulationResult"
            :result="simulationResult"
            :loading="loadingSimulation"
          />

          <!-- Supplier Recommendations -->
          <SupplierRecommendations
            v-if="simulationResult && supplierRecommendations.length > 0"
            :suppliers="supplierRecommendations"
            :simulation-result="simulationResult"
            @supplier-selected="handleSupplierSelected"
            :loading="loadingSuppliers"
          />

          <!-- Empty State -->
          <div v-if="!simulationResult && !loadingSimulation" class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">{{ $t('Masukkan parameter simulasi untuk melihat hasil') || 'Mulai simulasi untuk melihat hasil' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ChatAssistant from './Components/ChatAssistant.vue'
import SimulatorEngine from './Components/SimulatorEngine.vue'
import SimulationResults from './Components/SimulationResults.vue'
import SupplierRecommendations from './Components/SupplierRecommendations.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const commodities = ref(page.props.commodities || [])

const simulationResult = ref(null)
const supplierRecommendations = ref([])
const loadingSimulation = ref(false)
const loadingSuppliers = ref(false)

const handleStorySubmitted = async (params) => {
  // Params berisi: commodity_id, base_price, simulated_price, weight_in_basket, seasonal_factor, base_operational_cost
  await handleSimulationRequest(params)
}

const handleSimulationRequest = async (params) => {
  loadingSimulation.value = true
  
  try {
    const response = await fetch(route('simulator.calculate'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(params),
    })

    const data = await response.json()

    if (data.success) {
      simulationResult.value = data.simulation_result
      supplierRecommendations.value = data.supplier_recommendations || []
    } else {
      console.error('Simulation failed:', data.message)
    }
  } catch (error) {
    console.error('Error during simulation:', error)
  } finally {
    loadingSimulation.value = false
  }
}

const handleSupplierSelected = async (supplier) => {
  // Ketika user klik tombol "Simulasikan dengan supplier ini"
  loadingSimulation.value = true

  try {
    const response = await fetch(route('simulator.simulate-with-supplier'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        commodity_id: simulationResult.value.commodity.id,
        supplier_id: supplier.supplier_id,
        base_price: simulationResult.value.price_analysis.base_price,
        weight_in_basket: simulationResult.value.weighted_impact.weight_in_basket,
        seasonal_factor: simulationResult.value.weighted_impact.seasonal_factor,
        base_operational_cost: simulationResult.value.weighted_impact.new_operational_cost,
      }),
    })

    const data = await response.json()

    if (data.success) {
      simulationResult.value = data.simulation_result
    }
  } catch (error) {
    console.error('Error simulating with supplier:', error)
  } finally {
    loadingSimulation.value = false
  }
}
</script>
