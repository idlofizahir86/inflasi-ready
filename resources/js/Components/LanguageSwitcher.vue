<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import axios from 'axios';

const page = usePage();

const currentLocale = computed(() => page.props.locale || 'id');

const switchLocale = async (newLocale) => {
    if (newLocale === currentLocale.value) return;

    try {
        await axios.post('/set-locale', { locale: newLocale });
        // Full page reload to get fresh translations from server
        window.location.reload();
    } catch (err) {
        console.error('Failed to switch locale:', err);
    }
};
</script>

<template>
    <div class="flex items-center bg-slate-100 rounded-full p-0.5 gap-0.5">
        <button
            @click="switchLocale('id')"
            :class="[
                'px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide transition-all duration-200',
                currentLocale === 'id'
                    ? 'bg-emerald-800 text-white shadow-sm'
                    : 'text-slate-500 hover:text-slate-700'
            ]"
        >
            ID
        </button>
        <button
            @click="switchLocale('en')"
            :class="[
                'px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide transition-all duration-200',
                currentLocale === 'en'
                    ? 'bg-emerald-800 text-white shadow-sm'
                    : 'text-slate-500 hover:text-slate-700'
            ]"
        >
            EN
        </button>
    </div>
</template>
