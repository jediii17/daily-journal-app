<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, User, Mail, Lock, LockKeyhole } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
</script>

<template>
    <Head title="Daily Journal Registration" />

    <div
        class="bg-background-light font-display flex min-h-screen items-center justify-center p-4 dark:bg-neutral-950"
    >
        <!-- Main Card Container -->
        <div
            class="w-full max-w-[480px] overflow-hidden rounded-xl border border-neutral-100 bg-white p-6 shadow-xl dark:border-neutral-800 dark:bg-neutral-900"
        >
            <!-- Header back arrow -->
            <div
                class="-mx-2 -mt-2 flex items-center justify-between bg-transparent pb-4"
            >
                <Link
                    :href="login()"
                    class="flex size-12 shrink-0 cursor-pointer items-center justify-center rounded-full text-neutral-900 transition-colors hover:bg-neutral-100 dark:text-neutral-100 dark:hover:bg-neutral-800"
                >
                    <ArrowLeft class="size-6" />
                </Link>
                <h2
                    class="flex-1 pr-12 text-center text-lg leading-tight font-bold tracking-tight text-neutral-900 dark:text-neutral-100"
                >
                    Registration
                </h2>
            </div>

            <!-- Logo Section -->
            <div class="mt-2 mb-6 flex flex-col items-center">
                <h1
                    class="mb-1 text-center text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100"
                >
                    My Journal
                </h1>
                <p
                    class="mb-6 text-center text-base text-neutral-500 dark:text-neutral-400"
                >
                    Create an account and start capturing your days.
                </p>
            </div>

            <!-- Registration Form -->
            <Form
                v-bind="store.form()"
                :reset-on-success="['password', 'password_confirmation']"
                v-slot="{ errors, processing }"
                class="space-y-4"
            >
                <!-- Name Field -->
                <div class="flex flex-col gap-1.5">
                    <label
                        for="name"
                        class="px-1 text-sm font-semibold text-neutral-700 dark:text-neutral-300"
                    >
                        Name</label
                    >
                    <div class="relative">
                        <User
                            class="absolute top-1/2 left-4 size-5 -translate-y-1/2 text-neutral-400"
                        />
                        <input
                            id="name"
                            name="name"
                            required
                            autofocus
                            autocomplete="name"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-3.5 pr-4 pl-12 text-neutral-900 transition-all outline-none placeholder:text-neutral-400 focus:border-neutral-900 focus:ring-2 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:focus:border-neutral-100 dark:focus:ring-neutral-100"
                            placeholder="Jedidia Lemuel"
                            type="text"
                        />
                    </div>
                    <InputError :message="errors.name" />
                </div>

                <!-- Email Field -->
                <div class="flex flex-col gap-1.5">
                    <label
                        for="email"
                        class="px-1 text-sm font-semibold text-neutral-700 dark:text-neutral-300"
                        >Email Address</label
                    >
                    <div class="relative">
                        <Mail
                            class="absolute top-1/2 left-4 size-5 -translate-y-1/2 text-neutral-400"
                        />
                        <input
                            id="email"
                            name="email"
                            required
                            autocomplete="email"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-3.5 pr-4 pl-12 text-neutral-900 transition-all outline-none placeholder:text-neutral-400 focus:border-neutral-900 focus:ring-2 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:focus:border-neutral-100 dark:focus:ring-neutral-100"
                            placeholder="name@gmail.com"
                            type="email"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- Password Field -->
                <div class="flex flex-col gap-1.5">
                    <label
                        for="password"
                        class="px-1 text-sm font-semibold text-neutral-700 dark:text-neutral-300"
                        >Password</label
                    >
                    <div class="relative">
                        <Lock
                            class="absolute top-1/2 left-4 size-5 -translate-y-1/2 text-neutral-400"
                        />
                        <input
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-3.5 pr-4 pl-12 text-neutral-900 transition-all outline-none placeholder:text-neutral-400 focus:border-neutral-900 focus:ring-2 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:focus:border-neutral-100 dark:focus:ring-neutral-100"
                            placeholder="••••••••"
                            type="password"
                        />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <!-- Confirm Password Field -->
                <div class="flex flex-col gap-1.5">
                    <label
                        for="password_confirmation"
                        class="px-1 text-sm font-semibold text-neutral-700 dark:text-neutral-300"
                        >Confirm Password</label
                    >
                    <div class="relative">
                        <LockKeyhole
                            class="absolute top-1/2 left-4 size-5 -translate-y-1/2 text-neutral-400"
                        />
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-3.5 pr-4 pl-12 text-neutral-900 transition-all outline-none placeholder:text-neutral-400 focus:border-neutral-900 focus:ring-2 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:focus:border-neutral-100 dark:focus:ring-neutral-100"
                            placeholder="••••••••"
                            type="password"
                        />
                    </div>
                    <InputError :message="errors.password_confirmation" />
                </div>

                <!-- Sign Up Button -->
                <button
                    type="submit"
                    :disabled="processing"
                    class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-neutral-900 py-4 font-semibold text-white shadow-md transition-all hover:bg-black active:scale-[0.98] dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white"
                >
                    <Spinner v-if="processing" class="h-5 w-5" />
                    Sign up
                </button>
            </Form>

            <!-- Login Link -->
            <div
                class="-mx-6 mt-8 mb-2 border-t border-neutral-100 pt-6 text-center dark:border-neutral-800"
            >
                <p class="text-neutral-600 dark:text-neutral-400">
                    Already have an account?
                    <Link
                        :href="login()"
                        class="font-semibold text-neutral-900 hover:underline dark:text-neutral-100"
                        >Log in</Link
                    >
                </p>
            </div>
        </div>
    </div>
</template>
