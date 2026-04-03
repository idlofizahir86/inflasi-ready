<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { marked } from 'marked';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();

const props = defineProps({
    userManual: String,
    appOverview: String
});

const activeTab = ref('manual');

const compiledMarkdown = computed(() => {
    const content = activeTab.value === 'manual' ? props.userManual : props.appOverview;
    return marked.parse(content);
});
</script>

<template>
    <Head :title="t('Pusat Bantuan')" />
    
    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-emerald-900 font-headline">{{ t('Pusat Bantuan') }}</h1>
                    <p class="text-slate-500">{{ t('Dokumentasi lengkap Platform') }}</p>
                </div>
                <Link :href="route('dashboard')" class="text-sm font-bold text-emerald-700 hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span> {{ t('Kembali ke Dashboard') }}
                </Link>
            </div>

            <div class="flex gap-4 mb-6 border-b border-slate-200">
                <button @click="activeTab = 'manual'" 
                    :class="[activeTab === 'manual' ? 'border-emerald-600 text-emerald-700 border-b-2' : 'text-slate-400']"
                    class="pb-3 px-4 font-bold transition-all">
                    {{ t('User Manual') }}
                </button>
                <button @click="activeTab = 'overview'" 
                    :class="[activeTab === 'overview' ? 'border-emerald-600 text-emerald-700 border-b-2' : 'text-slate-400']"
                    class="pb-3 px-4 font-bold transition-all">
                    {{ t('Application Overview') }}
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 md:p-12">
                <article 
                    class="prose prose-emerald prose-slate max-w-none 
                    prose-headings:font-headline prose-headings:font-bold
                    prose-img:rounded-xl prose-code:text-emerald-600"
                    v-html="compiledMarkdown">
                </article>
            </div>
        </div>
    </div>
</template>