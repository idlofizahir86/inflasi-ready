<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(true);

// Menu items untuk sidebar
const menuItems = [
    {
        name: 'Dashboard',
        route: 'dashboard',
        icon: '📊',
        current: () => route().current('dashboard')
    },
    {
        name: 'Data Center',
        route: 'datacenter',
        icon: '🗄️',
        current: () => route().current('datacenter')
    },
    {
        name: 'Simulasi',
        route: 'simulation',
        icon: '🔄',
        current: () => route().current('simulation*')
    },
    {
        name: 'Supplier',
        route: 'simulation.suppliers',
        icon: '🏭',
        current: () => route().current('simulation.suppliers') || route().current('simulator.suppliers')
    },
    {
        name: 'API Settings',
        route: 'api-settings',
        icon: '⚙️',
        current: () => route().current('api-settings')
    },
    {
        name: 'Support',
        route: 'support',
        icon: '❓',
        current: () => route().current('support')
    }
];
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="sticky top-0 h-screen bg-gradient-to-b from-gray-800 to-gray-900 text-white shadow-lg transition-all duration-300"
        >
            <!-- Logo Section -->
            <div class="flex h-16 items-center justify-between px-4">
                <Link :href="route('dashboard')" v-if="sidebarOpen">
                    <ApplicationLogo class="h-9 w-auto fill-current" />
                </Link>
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="rounded-lg p-2 hover:bg-gray-700 transition-colors"
                >
                    <svg
                        class="h-5 w-5"
                        :class="sidebarOpen ? '' : 'rotate-180'"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="mt-8 space-y-2 px-2">
                <Link
                    v-for="item in menuItems"
                    :key="item.route"
                    :href="route(item.route)"
                    :class="[
                        item.current()
                            ? 'bg-indigo-600 text-white'
                            : 'text-gray-300 hover:bg-gray-700',
                        'group flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors'
                    ]"
                >
                    <span class="text-xl" :class="sidebarOpen ? 'mr-3' : ''">{{ item.icon }}</span>
                    <span v-if="sidebarOpen">{{ item.name }}</span>
                </Link>
            </nav>

            <!-- Footer -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-gray-700 p-4">
                <div v-if="sidebarOpen" class="mb-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase">Version</p>
                    <p class="text-sm font-medium">1.0.0</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation Bar -->
            <nav class="border-b border-gray-200 bg-white shadow-sm">
                <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex items-center">
                            <h1 v-if="$slots.header" class="text-lg font-semibold text-gray-900">
                                <!-- Page title goes here -->
                            </h1>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            v-for="item in menuItems"
                            :key="item.route"
                            :href="route(item.route)"
                            :active="item.current()"
                        >
                            {{ item.icon }} {{ item.name }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Header -->
            <header
                v-if="$slots.header"
                class="border-b border-gray-200 bg-white shadow-sm"
            >
                <div class="mx-auto max-w-full px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <div class="mx-auto max-w-full p-4 sm:p-6 lg:p-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
