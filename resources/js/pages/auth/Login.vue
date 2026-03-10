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

    <div class="bg-background-light dark:bg-neutral-950 font-display min-h-screen flex items-center justify-center p-6">
        <div
            class="w-full max-w-[400px] flex flex-col bg-white dark:bg-neutral-900 rounded-xl shadow-xl overflow-hidden border border-neutral-100 dark:border-neutral-800">

            <!-- Logo and titles -->
            <div class="px-6 pt-4 flex flex-col items-center py-4">
                <h1
                    class="text-neutral-900 dark:text-neutral-100 text-2xl font-bold leading-tight tracking-tight mb-1 text-center">
                    My Journal</h1>
                <p class="text-neutral-500 dark:text-neutral-400 text-base font-medium mb-8 text-center">
                    Welcome back. Let’s pick up where you left off.
                </p>

                <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                    {{ status }}
                </div>
            </div>

            <!-- Form -->
            <Form v-bind="store.form()" :reset-on-success="['password']" v-slot="{ errors, processing }"
                class="px-6 pb-8 space-y-5">
                <div class="flex flex-col gap-1.5">
                    <label for="email"
                        class="text-neutral-700 dark:text-neutral-300 text-sm font-semibold px-1">Email</label>
                    <div class="relative">
                        <input id="email" name="email" required autofocus autocomplete="email"
                            class="form-input w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-neutral-900 focus:border-neutral-900 dark:focus:ring-neutral-100 dark:focus:border-neutral-100 h-12 px-4 placeholder:text-neutral-400 text-base font-normal transition-colors"
                            placeholder="your@gmail.com" type="email" />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password"
                        class="text-neutral-700 dark:text-neutral-300 text-sm font-semibold px-1">Password</label>
                    <div class="relative">
                        <input id="password" name="password" required autocomplete="current-password"
                            class="form-input w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-neutral-900 focus:border-neutral-900 dark:focus:ring-neutral-100 dark:focus:border-neutral-100 h-12 px-4 placeholder:text-neutral-400 text-base font-normal transition-colors"
                            placeholder="••••••••" type="password" />
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

                <button type="submit" :disabled="processing"
                    class="w-full bg-neutral-900 hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white text-white font-bold py-3.5 px-4 rounded-lg transition-colors flex items-center justify-center gap-2 mt-2 disabled:opacity-75">
                    <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                    <span>Log in</span>
                </button>
            </Form>

            <!-- Bottom section -->
            <div v-if="canRegister"
                class="bg-neutral-50 dark:bg-neutral-800/50 p-6 text-center border-t border-neutral-100 dark:border-neutral-800">
                <p class="text-neutral-600 dark:text-neutral-400 text-sm">
                    Don’t have an account yet?
                    <Link :href="register()"
                        class="font-bold hover:underline ml-1 text-neutral-900 dark:text-neutral-100">Start your journal</Link>
                </p>
            </div>
        </div>

    </div>
</template>
