<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div class="text-center">
            <Link href="/" class="flex flex-col items-center gap-2">
                <ApplicationLogo class="h-12 w-12 fill-current text-emerald-800" />
                <h1 class="text-2xl tracking-tight text-emerald-900 font-headline">
                    <span class="font-black">ARTHA</span><span class="font-light">DATA</span>
                </h1>
            </Link>
            <div class="mt-3">
                <LanguageSwitcher />
            </div>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
