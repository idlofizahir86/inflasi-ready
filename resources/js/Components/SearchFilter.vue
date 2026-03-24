<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    searchFields: {
        type: Array,
        default: () => ['name', 'category']
    }
});

const emit = defineEmits(['update:filtered']);

const searchQuery = ref('');
const selectedCategory = ref('');
const selectedStatus = ref('');

// Get unique categories from items
const categories = computed(() => {
    const cats = new Set();
    props.items.forEach(item => {
        if (item.category) cats.add(item.category);
    });
    return Array.from(cats).sort();
});

// Get unique statuses from items
const statuses = computed(() => {
    const stats = new Set();
    props.items.forEach(item => {
        if (item.status) stats.add(item.status);
    });
    return Array.from(stats).sort();
});

// Filter logic
const filteredItems = computed(() => {
    let result = props.items;

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(item => {
            return props.searchFields.some(field => {
                const value = item[field];
                return value && value.toString().toLowerCase().includes(query);
            });
        });
    }

    // Category filter
    if (selectedCategory.value) {
        result = result.filter(item => item.category === selectedCategory.value);
    }

    // Status filter
    if (selectedStatus.value) {
        result = result.filter(item => item.status === selectedStatus.value);
    }

    return result;
});

// Update parent on filter change
const updateFiltered = () => {
    emit('update:filtered', filteredItems.value);
};

// Watch for changes
const handleSearch = () => {
    updateFiltered();
};

const handleCategoryChange = () => {
    updateFiltered();
};

const handleStatusChange = () => {
    updateFiltered();
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedStatus.value = '';
    updateFiltered();
};
</script>

<template>
    <div class="bg-white rounded-lg p-6 shadow-sm border border-slate-100 mb-6">
        <div class="flex flex-col lg:flex-row gap-4 items-end">
            <!-- Search Input -->
            <div class="flex-1">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Search</label>
                <div class="relative">
                    <input 
                        v-model="searchQuery"
                        @input="handleSearch"
                        type="text" 
                        placeholder="Search by name or category..."
                        class="w-full px-4 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    />
                    <span class="material-symbols-outlined absolute right-3 top-3 text-slate-400 text-xl">search</span>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="w-full lg:w-48">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Category</label>
                <select 
                    v-model="selectedCategory"
                    @change="handleCategoryChange"
                    class="w-full px-4 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full lg:w-48">
                <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Status</label>
                <select 
                    v-model="selectedStatus"
                    @change="handleStatusChange"
                    class="w-full px-4 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option v-for="stat in statuses" :key="stat" :value="stat">{{ stat }}</option>
                </select>
            </div>

            <!-- Clear Button -->
            <button 
                @click="clearFilters"
                class="px-4 py-3 bg-slate-100 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">close</span>
                Clear
            </button>
        </div>

        <!-- Results Count -->
        <div class="mt-4 pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-500">
                Showing <span class="font-bold text-emerald-700">{{ filteredItems.length }}</span> of <span class="font-bold">{{ items.length }}</span> records
            </p>
        </div>
    </div>
</template>

<style scoped>
</style>
