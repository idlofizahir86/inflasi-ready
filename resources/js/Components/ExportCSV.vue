<script setup>
import { defineProps, ref } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    filename: {
        type: String,
        default: 'export.csv'
    }
});

const isExporting = ref(false);

const exportToCSV = () => {
    if (!props.items || props.items.length === 0) return;

    isExporting.value = true;

    try {
        // Get all keys from items
        const keys = Object.keys(props.items[0]);
        
        // Create CSV header
        const header = keys.map(key => `"${key}"`).join(',');
        
        // Helper function to extract value from nested objects
        const extractValue = (value) => {
            if (value === null || value === undefined) return '';
            if (typeof value === 'object') {
                // Jika object punya property 'name', gunakan itu
                if (value.name) return value.name;
                // Jika object punya property 'title', gunakan itu
                if (value.title) return value.title;
                // Jika object punya property 'value', gunakan itu
                if (value.value) return value.value;
                // Fallback: stringify tapi hanya ambil keys visible
                return Object.values(value).filter(v => typeof v !== 'object').join(' ');
            }
            return String(value);
        };
        
        // Create CSV rows
        const rows = props.items.map(item => {
            return keys.map(key => {
                const value = item[key];
                const stringValue = extractValue(value).replace(/"/g, '""');
                return `"${stringValue}"`;
            }).join(',');
        });

        // Combine header and rows
        const csv = [header, ...rows].join('\n');

        // Create blob and download
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', props.filename);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error('Error exporting CSV:', error);
    } finally {
        isExporting.value = false;
    }
};
</script>

<template>
    <button 
        @click="exportToCSV"
        :disabled="isExporting || !items || items.length === 0"
        class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest hover:text-emerald-900 transition-colors flex items-center gap-2 disabled:opacity-50">
        <span class="material-symbols-outlined text-sm">{{ isExporting ? 'hourglass_top' : 'download' }}</span>
        {{ isExporting ? 'Exporting...' : 'Export CSV' }}
    </button>
</template>

<style scoped>
</style>
