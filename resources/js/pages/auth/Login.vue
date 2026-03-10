<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Daily Journal Login" />

    <div
        class="bg-background-light font-display flex min-h-screen items-center justify-center p-6 dark:bg-neutral-950"
    >
        <div
            class="flex w-full max-w-[400px] flex-col overflow-hidden rounded-xl border border-neutral-100 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900"
        >
            <!-- Logo and titles -->
            <div class="flex flex-col items-center px-6 py-4 pt-4">
                <h1
                    class="mb-1 text-center text-2xl leading-tight font-bold tracking-tight text-neutral-900 dark:text-neutral-100"
                >
                    My Journal
                </h1>
                <p
                    class="mb-8 text-center text-base font-medium text-neutral-500 dark:text-neutral-400"
                >
                    Welcome back. Let’s pick up where you left off.
                </p>

                <div
                    v-if="status"
                    class="mb-4 text-center text-sm font-medium text-green-600"
                >
                    {{ status }}
                </div>
            </div>

            <!-- Form -->
            <Form
                v-bind="store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="space-y-5 px-6 pb-8"
            >
                <div class="flex flex-col gap-1.5">
                    <label
                        for="email"
                        class="px-1 text-sm font-semibold text-neutral-700 dark:text-neutral-300"
                        >Email</label
                    >
                    <div class="relative">
                        <input
                            id="email"
                            name="email"
                            required
                            autofocus
                            autocomplete="email"
                            class="form-input h-12 w-full rounded-lg border border-neutral-200 bg-neutral-50 px-4 text-base font-normal text-neutral-900 transition-colors placeholder:text-neutral-400 focus:border-neutral-900 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:focus:border-neutral-100 dark:focus:ring-neutral-100"
                            placeholder="your@gmail.com"
                            type="email"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label
                        for="password"
                        class="px-1 text-sm font-semibold text-neutral-700 dark:text-neutral-300"
                        >Password</label
                    >
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="form-input h-12 w-full rounded-lg border border-neutral-200 bg-neutral-50 px-4 text-base font-normal text-neutral-900 transition-colors placeholder:text-neutral-400 focus:border-neutral-900 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:focus:border-neutral-100 dark:focus:ring-neutral-100"
                            placeholder="••••••••"
                            type="password"
                        />
                    </div>
                    <InputError :message="errors.password" />
                </div>
                <!-- 
                <div class="flex items-center justify-between py-1">
                    <div class="flex items-center">
                        <input
                            class="h-4 w-4 rounded border-neutral-300 text-primary focus:ring-primary bg-neutral-50 dark:bg-neutral-800 dark:border-neutral-700"
                            id="remember" name="remember" type="checkbox" />
                        <label class="ml-2 block text-sm text-neutral-600 dark:text-neutral-400" for="remember">Remember
                            me</label>
                    </div>
                    <div class="text-sm">
                        <Link v-if="canResetPassword" :href="request()"
                            class="font-medium text-primary hover:underline">Forgot password?</Link>
                    </div>
                </div> -->

                <button
                    type="submit"
                    :disabled="processing"
                    class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-neutral-900 px-4 py-3.5 font-bold text-white transition-colors hover:bg-black disabled:opacity-75 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white"
                >
                    <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                    <span>Log in</span>
                </button>
            </Form>

            <!-- Bottom section -->
            <div
                v-if="canRegister"
                class="border-t border-neutral-100 bg-neutral-50 p-6 text-center dark:border-neutral-800 dark:bg-neutral-800/50"
            >
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    Don’t have an account yet?
                    <Link
                        :href="register()"
                        class="ml-1 font-bold text-neutral-900 hover:underline dark:text-neutral-100"
                        >Start your journal</Link
                    >
                </p>
            </div>
        </div>
    </div>
</template>
