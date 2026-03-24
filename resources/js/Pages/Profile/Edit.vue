<script setup>
import StitchLayout from '@/Layouts/StitchLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile Settings" />

    <StitchLayout>
        <div class="space-y-12">
            <!-- Header -->
            <header class="mb-8">
                <h2 class="text-3xl font-extrabold text-on-surface tracking-tight font-headline">Profile Settings</h2>
                <p class="text-on-surface-variant font-body mt-2">Manage your account information and security preferences</p>
            </header>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Card -->
                <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border border-slate-100 h-fit">
                    <div class="flex flex-col items-center text-center">
                        <div class="h-24 w-24 rounded-full bg-gradient-to-br from-primary to-primary-container overflow-hidden mb-4 border-4 border-white shadow-lg">
                            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYOMV4JO4-K2NzCrBb7ybin0weQxK5DRU2q1zpd6VodS3-psISjVHY38q7Sg7NWBq16QvC17ksbhAtLm0ENcqqyJy-4kfwHpgqRF0TFt19L2a2jBF-yVw35G7kTXo6NNB2Swv5eP0hwuI_FqLQqMZzEqf2sV7QdAYuvOOxjoCbwN2_6ackZ-awG_sv2F7W_mLpO1AiUacbxAQnOJe2N_rmXsk5SERVghOJdivgz57c69LEL0zr7S6ypqn_m-5k5PsoQkpAATg8ZwU" alt="Profile" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-xl font-bold font-headline text-on-surface">Admin User</h3>
                        <p class="text-sm text-on-surface-variant mt-1">Administrator</p>
                        <div class="mt-4 flex items-center justify-center gap-2 px-3 py-1 bg-primary/10 rounded-full">
                            <span class="h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                            <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Active</span>
                        </div>
                    </div>
                    
                    <div class="mt-8 space-y-4 border-t border-slate-100 pt-6">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Member Since</p>
                            <p class="text-sm font-semibold text-on-surface">January 15, 2024</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Last Updated</p>
                            <p class="text-sm font-semibold text-on-surface">March 24, 2026</p>
                        </div>
                    </div>

                    <button class="w-full mt-6 py-3 bg-primary text-white font-headline font-bold rounded-lg hover:bg-primary-container transition-all active:scale-95">
                        Upload Photo
                    </button>
                </div>

                <!-- Forms -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Update Profile Information -->
                    <section class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border border-slate-100">
                        <h3 class="text-xl font-bold font-headline text-on-surface mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">person</span>
                            Profile Information
                        </h3>
                        <UpdateProfileInformationForm :mustVerifyEmail="mustVerifyEmail" :status="status" />
                    </section>

                    <!-- Update Password -->
                    <section class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border border-slate-100">
                        <h3 class="text-xl font-bold font-headline text-on-surface mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">lock</span>
                            Update Password
                        </h3>
                        <UpdatePasswordForm />
                    </section>

                    <!-- Delete Account -->
                    <section class="bg-error/5 p-8 rounded-xl shadow-sm border border-error/20">
                        <h3 class="text-xl font-bold font-headline text-error mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined">delete</span>
                            Delete Account
                        </h3>
                        <DeleteUserForm />
                    </section>
                </div>
            </div>
        </div>
    </StitchLayout>
</template>

<style scoped>
.font-headline { font-family: 'Lexend', sans-serif; }
.font-body { font-family: 'Inter', sans-serif; }
</style>
