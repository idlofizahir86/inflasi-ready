<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    demoEmail: {
        type: String,
        default: null,
    },
    demoPassword: {
        type: String,
        default: null,
    },
    showDemoHint: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

onMounted(() => {
    // Check if demo credentials are provided (after logout)
    const urlParams = new URLSearchParams(window.location.search);
    const demoEmail = urlParams.get('demo_email');
    const demoPassword = urlParams.get('demo_password');
    
    if (demoEmail && demoPassword) {
        form.email = decodeURIComponent(demoEmail);
        form.password = decodeURIComponent(demoPassword);
        form.remember = true;
    }
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <!-- Demo Account Hint -->
        <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200">
            <p class="text-xs font-semibold text-emerald-900 uppercase tracking-widest mb-2">Demo Account</p>
            <p class="text-sm text-emerald-800 font-medium">
                Email: <code class="bg-white px-2 py-1 rounded text-emerald-700 font-mono">akun-demo@pidi.id</code>
            </p>
            <p class="text-sm text-emerald-800 font-medium">
                Password: <code class="bg-white px-2 py-1 rounded text-emerald-700 font-mono">123456</code>
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
