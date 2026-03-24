<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true
    },
    totalPages: {
        type: Number,
        required: true
    },
    totalItems: {
        type: Number,
        required: true
    },
    itemsPerPage: {
        type: Number,
        default: 10
    }
});

const emit = defineEmits(['update:page']);

const goToPage = (page) => {
    if (page >= 1 && page <= props.totalPages) {
        emit('update:page', page);
    }
};

const getPageNumbers = () => {
    const pages = [];
    const maxVisible = 5;
    
    if (props.totalPages <= maxVisible) {
        for (let i = 1; i <= props.totalPages; i++) {
            pages.push(i);
        }
    } else {
        const start = Math.max(1, props.currentPage - 2);
        const end = Math.min(props.totalPages, props.currentPage + 2);
        
        if (start > 1) pages.push(1);
        if (start > 2) pages.push('...');
        
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }
        
        if (end < props.totalPages - 1) pages.push('...');
        if (end < props.totalPages) pages.push(props.totalPages);
    }
    
    return pages;
};

const currentStart = computed(() => (props.currentPage - 1) * props.itemsPerPage + 1);
const currentEnd = computed(() => Math.min(props.currentPage * props.itemsPerPage, props.totalItems));
</script>

<template>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-8 py-5 bg-slate-50/50 border-t border-slate-100">
        <!-- Items Info -->
        <div class="text-sm text-slate-500">
            Showing <span class="font-bold">{{ currentStart }}</span> to <span class="font-bold">{{ currentEnd }}</span> of <span class="font-bold">{{ totalItems }}</span> items
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center gap-2">
            <button 
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition-all disabled:opacity-30 disabled:hover:bg-white">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            
            <div class="flex gap-1 shadow-sm border border-slate-200 rounded-xl bg-white p-1">
                <template v-for="page in getPageNumbers()" :key="page">
                    <button 
                        v-if="page !== '...'"
                        @click="goToPage(page)"
                        :disabled="page === '...'"
                        :class="[
                            currentPage === page ? 'bg-emerald-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50',
                            page === '...' ? 'cursor-default' : 'cursor-pointer'
                        ]"
                        class="w-8 h-8 flex items-center justify-center rounded-lg font-bold text-xs transition-all">
                        {{ page }}
                    </button>
                    <span v-else class="w-8 h-8 flex items-center justify-center text-slate-400">...</span>
                </template>
            </div>

            <button 
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition-all disabled:opacity-30 disabled:hover:bg-white">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
        </div>
    </div>
</template>

<script>
import { computed } from 'vue';
</script>

<style scoped>
</style>
