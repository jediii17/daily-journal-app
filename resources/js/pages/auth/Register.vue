<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import { ArrowLeft, FileEdit, User, Mail, Lock, LockKeyhole } from 'lucide-vue-next';
import { login } from '@/routes';
import { store } from '@/routes/register';
</script>

<template>

    <Head title="Daily Journal Registration" />

    <div class="bg-background-light dark:bg-slate-950 font-display min-h-screen flex items-center justify-center p-4">
        <!-- Main Card Container -->
        <div
            class="w-full max-w-[480px] bg-white dark:bg-slate-900 rounded-xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-800 p-6">

            <!-- Header back arrow -->
            <div class="flex items-center bg-transparent pb-4 justify-between -mt-2 -mx-2">
                <Link :href="login()"
                    class="text-slate-900 dark:text-slate-100 flex size-12 shrink-0 items-center justify-center cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                    <ArrowLeft class="size-6" />
                </Link>
                <h2
                    class="text-slate-900 dark:text-slate-100 text-lg font-bold leading-tight tracking-tight flex-1 text-center pr-12">
                    Registration</h2>
            </div>

            <!-- Logo Section -->
            <div class="flex flex-col items-center mb-6 mt-2">
                <h1 class="text-slate-900 dark:text-slate-100 text-2xl font-bold tracking-tight mb-1 text-center">My
                    Journal</h1>
                <p class="text-slate-500 dark:text-slate-400 text-base mb-6 text-center">Create an account</p>
            </div>

            <!-- Registration Form -->
            <Form v-bind="store.form()" :reset-on-success="['password', 'password_confirmation']"
                v-slot="{ errors, processing }" class="space-y-4">
                <!-- Name Field -->
                <div class="flex flex-col gap-1.5">
                    <label for="name" class="text-slate-700 dark:text-slate-300 text-sm font-semibold px-1">
                        Name</label>
                    <div class="relative">
                        <User class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 size-5" />
                        <input id="name" name="name" required autofocus autocomplete="name"
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400"
                            placeholder="Jedidia Lemuel" type="text" />
                    </div>
                    <InputError :message="errors.name" />
                </div>

                <!-- Email Field -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-slate-700 dark:text-slate-300 text-sm font-semibold px-1">Email
                        Address</label>
                    <div class="relative">
                        <Mail class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 size-5" />
                        <input id="email" name="email" required autocomplete="email"
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400"
                            placeholder="name@gmail.com" type="email" />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- Password Field -->
                <div class="flex flex-col gap-1.5">
                    <label for="password"
                        class="text-slate-700 dark:text-slate-300 text-sm font-semibold px-1">Password</label>
                    <div class="relative">
                        <Lock class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 size-5" />
                        <input id="password" name="password" required autocomplete="new-password"
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400"
                            placeholder="••••••••" type="password" />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <!-- Confirm Password Field -->
                <div class="flex flex-col gap-1.5">
                    <label for="password_confirmation"
                        class="text-slate-700 dark:text-slate-300 text-sm font-semibold px-1">Confirm Password</label>
                    <div class="relative">
                        <LockKeyhole class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 size-5" />
                        <input id="password_confirmation" name="password_confirmation" required
                            autocomplete="new-password"
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400"
                            placeholder="••••••••" type="password" />
                    </div>
                    <InputError :message="errors.password_confirmation" />
                </div>

                <!-- Sign Up Button -->
                <button type="submit" :disabled="processing"
                    class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-4 rounded-xl shadow-md transition-all active:scale-[0.98] mt-4 flex items-center justify-center gap-2">
                    <Spinner v-if="processing" class="h-5 w-5" />
                    Sign up
                </button>
            </Form>

            <!-- Login Link -->
            <div class="mt-8 text-center pt-6 border-t border-slate-100 dark:border-slate-800 -mx-6 mb-2">
                <p class="text-slate-600 dark:text-slate-400">
                    Already have an account?
                    <Link :href="login()" class="text-primary font-semibold hover:underline">Log in</Link>
                </p>
            </div>
        </div>

    </div>
</template>
