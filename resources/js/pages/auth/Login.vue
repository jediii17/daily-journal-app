<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Spinner } from '@/components/ui/spinner';
import { ArrowLeft, BookOpen } from 'lucide-vue-next';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>

    <Head title="Daily Journal Login" />

    <div class="bg-background-light dark:bg-slate-950 font-display min-h-screen flex items-center justify-center p-4">
        <div
            class="w-full max-w-[400px] flex flex-col bg-white dark:bg-slate-900 rounded-xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-800">
            <!-- Header back arrow -->
            <div class="flex items-center bg-transparent p-4 pb-2 justify-between">
                <Link href="/"
                    class="text-slate-900 dark:text-slate-100 flex size-12 shrink-0 items-center justify-center cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                    <ArrowLeft class="size-6" />
                </Link>
                <h2
                    class="text-slate-900 dark:text-slate-100 text-lg font-bold leading-tight tracking-tight flex-1 text-center pr-12">
                    Login</h2>
            </div>

            <!-- Logo and titles -->
            <div class="px-6 pt-4 flex flex-col items-center">
                <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <BookOpen class="size-10 text-primary" />
                </div>
                <h1
                    class="text-slate-900 dark:text-slate-100 text-2xl font-bold leading-tight tracking-tight mb-1 text-center">
                    My Journal</h1>
                <p class="text-slate-500 dark:text-slate-400 text-base font-medium mb-8 text-center">Welcome back</p>

                <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                    {{ status }}
                </div>
            </div>

            <!-- Form -->
            <Form v-bind="store.form()" :reset-on-success="['password']" v-slot="{ errors, processing }"
                class="px-6 pb-8 space-y-5">
                <div class="flex flex-col gap-1.5">
                    <label for="email"
                        class="text-slate-700 dark:text-slate-300 text-sm font-semibold px-1">Email</label>
                    <div class="relative">
                        <input id="email" name="email" required autofocus autocomplete="email"
                            class="form-input w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary h-12 px-4 placeholder:text-slate-400 text-base font-normal transition-colors"
                            placeholder="your@email.com" type="email" />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password"
                        class="text-slate-700 dark:text-slate-300 text-sm font-semibold px-1">Password</label>
                    <div class="relative">
                        <input id="password" name="password" required autocomplete="current-password"
                            class="form-input w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-primary focus:border-primary h-12 px-4 placeholder:text-slate-400 text-base font-normal transition-colors"
                            placeholder="••••••••" type="password" />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between py-1">
                    <div class="flex items-center">
                        <input
                            class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-700"
                            id="remember" name="remember" type="checkbox" />
                        <label class="ml-2 block text-sm text-slate-600 dark:text-slate-400" for="remember">Remember
                            me</label>
                    </div>
                    <div class="text-sm">
                        <Link v-if="canResetPassword" :href="request()"
                            class="font-medium text-primary hover:underline">Forgot password?</Link>
                    </div>
                </div>

                <button type="submit" :disabled="processing"
                    class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-4 rounded-lg transition-colors flex items-center justify-center gap-2 mt-2 disabled:opacity-75">
                    <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                    <span>Log in</span>
                </button>
            </Form>

            <!-- Bottom section -->
            <div v-if="canRegister"
                class="bg-slate-50 dark:bg-slate-800/50 p-6 text-center border-t border-slate-100 dark:border-slate-800">
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                    Don't have an account?
                    <Link :href="register()" class="text-primary font-bold hover:underline ml-1">Register</Link>
                </p>
            </div>
        </div>

        <!-- Decorative line at bottom -->
        <div class="fixed bottom-0 left-0 right-0 h-1 bg-primary/20">
            <div class="h-full bg-primary w-1/3"></div>
        </div>
    </div>
</template>
