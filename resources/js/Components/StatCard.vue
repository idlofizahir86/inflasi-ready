<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    trend: String,
    icon: String,
    color: {
        type: String,
        default: 'text-primary'
    },
    variant: {
        type: String,
        default: 'default',
        validator: (v) => ['default', 'accent', 'error'].includes(v)
    }
});

const bgColor = computed(() => {
    if (props.variant === 'accent') return 'bg-primary-container';
    if (props.variant === 'error') return 'bg-error/5';
    return 'bg-surface-container-lowest';
});

const textColor = computed(() => {
    if (props.variant === 'accent') return 'text-on-primary-container';
    if (props.variant === 'error') return 'text-error';
    return 'text-on-surface';
});
</script>

<template>
    <div :class="[bgColor, 'p-6 rounded-xl shadow-sm border transition-all hover:shadow-md']">
        <div v-if="variant === 'accent'" class="border-primary-container/20">
            <p :class="['text-on-primary-container/70 text-sm font-medium mb-2']">{{ label }}</p>
        </div>
        <div v-else-if="variant === 'error'" class="border-error/20">
            <p class="text-error/70 text-sm font-medium mb-2">{{ label }}</p>
        </div>
        <div v-else class="border-slate-100">
            <p class="text-on-surface-variant text-sm font-medium mb-2">{{ label }}</p>
        </div>

        <div class="flex items-baseline gap-2 mb-2">
            <span :class="[props.color, 'text-3xl font-headline font-bold']">{{ value }}</span>
            <span v-if="trend" :class="[props.color, 'text-xs font-bold px-1.5 py-0.5 rounded bg-white/20']">
                {{ trend }}
            </span>
            <span v-if="icon" class="material-symbols-outlined text-lg ml-1" :class="props.color">
                {{ icon }}
            </span>
        </div>

        <slot></slot>
    </div>
</template>

<style scoped>
</style>
