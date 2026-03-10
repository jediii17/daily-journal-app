<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { ref, watch, nextTick } from 'vue';
import 'quill/dist/quill.snow.css';
import Quill from 'quill';
import { BookOpen, Search, Plus, Save, History } from 'lucide-vue-next';

const props = defineProps<{
    entries: any[];
    search?: string;
    flash?: {
        success?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'My Journal', href: '/dashboard' }
];

const form = useForm({
    title: '',
    date: new Date().toISOString().split('T')[0],
    body: '',
});


// Create entry modal
const isCreateModalOpen = ref(false);

// Editor functionality
const editorContainer = ref<HTMLElement | null>(null);
const toolbarContainer = ref<HTMLElement | null>(null);
let quill: Quill | null = null;

// Word and character counts
const wordCount = ref(0);
const charCount = ref(0);

// Update word and character counts
const updateCounts = () => {
    if (!quill) return;
    const text = quill.getText();
    charCount.value = text.trim() === "" ? 0 : text.length - 1;
    wordCount.value = text.trim() === "" ? 0 : text.trim().split(/\s+/).length;
};

// Initialize Quill editor
const initQuill = () => {
    if (editorContainer.value && toolbarContainer.value && !quill) {
        quill = new Quill(editorContainer.value, {
            theme: 'snow',
            placeholder: 'Write your thoughts here...',
            modules: {
                toolbar: toolbarContainer.value
            }
        });

        quill.on('text-change', () => {
            if (quill) {
                form.body = quill.root.innerHTML;
                updateCounts();
            }
        });

        quill.root.focus();
        if (form.body) {
            quill.root.innerHTML = form.body;
        } else {
            form.body = quill.root.innerHTML;
        }
        updateCounts();
    }
};

watch(isCreateModalOpen, async (isOpen) => {
    if (isOpen) {
        await nextTick();
        initQuill();
    } else {
        quill = null; // Reset when modal closes
    }
});

// Format date
const formatDate = (dateString: string) => {
    const options: Intl.DateTimeFormatOptions = { month: 'short', day: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

// format full date
const formatFullDate = (dateString: string) => {
    const options: Intl.DateTimeFormatOptions = { month: 'long', day: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

// Strip tags and limit text
const stripTagsAndLimit = (html: string, limit = 150) => {
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    const text = tmp.textContent || tmp.innerText || '';
    if (text.length <= limit) return text;
    return text.substring(0, limit) + '...';
};

</script>

<template>

    <Head title="My Journal - Home" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden font-display text-slate-900 dark:text-slate-100">
            <div class="layout-container flex h-full grow flex-col">
                <div class="px-4 md:px-10 lg:px-40 flex flex-1 justify-center py-5">
                    <div class="layout-content-container flex flex-col max-w-[960px] flex-1">

                        <!-- Header Area -->
                        <header
                            class="flex flex-col md:flex-row items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 dark:border-slate-800 px-4 md:px-10 py-3 gap-4 md:gap-0">
                            <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-start">
                                <div class="flex items-center gap-4">
                                    <div class="size-6 text-primary flex items-center justify-center">
                                        <BookOpen class="size-6 text-primary" />
                                    </div>
                                    <a href="/dashboard"
                                        class="text-xl font-bold leading-tight tracking-[-0.015em] font-serif">My
                                        Journal</a>
                                </div>
                            </div>
                            <div class="flex w-full md:w-auto flex-1 justify-end gap-4 md:gap-6 items-center">
                                <form @submit.prevent="submitSearch"
                                    class="flex flex-col min-w-40 h-10 max-w-64 w-full md:w-auto">
                                    <div
                                        class="flex w-full flex-1 items-stretch rounded-full h-full bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-700">
                                        <button type="submit"
                                            class="text-slate-400 flex items-center justify-center pl-4 pr-2">
                                            <Search class="size-5" />
                                        </button>
                                        <input v-model="searchForm.search"
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-r-full focus:outline-0 focus:ring-0 border-none bg-transparent h-full placeholder:text-slate-400 px-2 text-sm font-normal leading-normal"
                                            placeholder="Search entries..." />
                                    </div>
                                </form>

                            </div>
                        </header>

                        <main class="flex-1 py-8 px-4 md:px-10">

                            <p v-if="props.entries.length === 0"
                                class="text-slate-500 dark:text-slate-400 italic bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-200 dark:border-slate-800 text-center">
                                <span v-if="props.search">No entries found matching your search.</span>
                                <span v-else>No entries yet. Start writing your first journal!</span>
                            </p>

                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <a v-for="entry in props.entries" :key="entry.id"
                                    class="relative flex flex-col p-6 rounded-2xl bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-800 hover:border-primary/50 dark:hover:border-primary/50 hover:shadow-md transition-all group flex-1 cursor-pointer"
                                    :href="'/entries/' + entry.id">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4
                                            class="font-bold text-[1.15rem] leading-tight text-slate-800 dark:text-slate-100 group-hover:text-primary transition-colors pr-10">
                                            {{ entry.title }}</h4>
                                        <span
                                            class="text-xs font-semibold text-slate-500 bg-slate-100 dark:bg-slate-800 dark:text-slate-400 rounded-full px-2.5 py-1 whitespace-nowrap">{{
                                                formatDate(entry.date) }}</span>
                                    </div>
                                    <p
                                        class="text-[0.95rem] text-slate-600 dark:text-slate-400 flex-1 line-clamp-3 leading-relaxed">
                                        {{ stripTagsAndLimit(entry.body, 120) }}</p>

                                    <!-- Edit Quick Action -->
                                    <button @click="(e) => openEditModal(entry, e)" aria-label="Edit Entry"
                                        class="absolute top-4 right-4 p-2 rounded-full opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all hover:bg-slate-100 dark:hover:bg-slate-800 bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 pointer-events-auto">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>
                                    </button>
                                </a>
                            </div>

                        </main>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<style>
/* Adjust Quill Editor to match styling */
.quill-editor-content.ql-container {
    font-family: 'Inter', sans-serif;
    font-size: 16px;
}

.ql-editor {
    padding: 0;
}
</style>
