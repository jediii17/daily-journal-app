<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { ArrowLeft, Edit3, Save } from 'lucide-vue-next';
import { ref, watch, nextTick } from 'vue';
import 'quill/dist/quill.snow.css';
import Quill from 'quill';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    entry: any;
    flash?: {
        success?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'My Journal', href: '/dashboard' }
];

const formatFullDate = (dateString: string) => {
    const options: Intl.DateTimeFormatOptions = { weekday: 'long', month: 'long', day: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

// Edit functionality
const isEditModalOpen = ref(false);

const editForm = useForm({
    title: props.entry.title,
    date: props.entry.date.split('T')[0],
    body: props.entry.body,
});

const editEditorContainer = ref<HTMLElement | null>(null);
const editToolbarContainer = ref<HTMLElement | null>(null);
let editQuill: Quill | null = null;
const editWordCount = ref(0);
const editCharCount = ref(0);

const updateEditCounts = () => {
    if (!editQuill) return;
    const text = editQuill.getText();
    editCharCount.value = text.trim() === "" ? 0 : text.length - 1;
    editWordCount.value = text.trim() === "" ? 0 : text.trim().split(/\s+/).length;
};

const initEditQuill = () => {
    if (editEditorContainer.value && editToolbarContainer.value && !editQuill) {
        editQuill = new Quill(editEditorContainer.value, {
            theme: 'snow',
            placeholder: 'Edit your thoughts...',
            modules: {
                toolbar: editToolbarContainer.value
            }
        });

        editQuill.on('text-change', () => {
            if (editQuill) {
                editForm.body = editQuill.root.innerHTML;
                updateEditCounts();
            }
        });

        if (editForm.body) {
            editQuill.root.innerHTML = editForm.body;
        }
        updateEditCounts();
    }
};

watch(isEditModalOpen, async (isOpen) => {
    if (isOpen) {
        // Reset form variables if user cancels and re-opens
        editForm.title = props.entry.title;
        editForm.date = props.entry.date.split('T')[0];
        editForm.body = props.entry.body;

        await nextTick();
        initEditQuill();
    } else {
        editQuill = null;
    }
});

const openEditModal = () => {
    isEditModalOpen.value = true;
};

const submitEdit = () => {
    if (editQuill && editQuill.getText().trim() === '') {
        editForm.body = '';
    }
    editForm.patch(`/entries/${props.entry.id}`, {
        onSuccess: () => {
            isEditModalOpen.value = false;
        }
    });
};

</script>

<template>

    <Head :title="props.entry.title + ' - My Journal'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden font-display text-neutral-900 dark:text-neutral-100">
            <div class="layout-container flex h-full grow flex-col">
                <div class="px-4 md:px-10 lg:px-40 flex flex-1 justify-center py-5">
                    <div class="layout-content-container flex flex-col max-w-[960px] flex-1">

                        <!-- Header Area -->
                        <header
                            class="flex flex-col md:flex-row items-center justify-between whitespace-nowrap border-b border-solid border-neutral-200 dark:border-neutral-800 px-4 md:px-10 py-3 gap-4 md:gap-0">
                            <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-start">
                                <div class="flex items-center gap-4">
                                    <Link href="/dashboard"
                                        class="size-6 text-neutral-700 dark:text-neutral-200 hover:opacity-80 transition-opacity flex items-center justify-center">
                                        <ArrowLeft class="size-6" />
                                    </Link>
                                    <Link href="/dashboard"
                                        class="text-xl font-bold leading-tight tracking-[-0.015em] font-serif">My
                                        Journal</Link>
                                </div>
                            </div>

                            <!-- Edit Button -->
                            <div class="flex w-full md:w-auto flex-1 justify-end items-center">
                                <Button @click="openEditModal" variant="outline"
                                    class="rounded-full shadow-sm font-semibold h-9 px-4 flex items-center gap-2 bg-neutral-900 text-white hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white">
                                    <Edit3 class="size-4" />
                                    Edit Entry
                                </Button>
                            </div>
                        </header>

                        <main class="flex-1 py-12 px-4 md:px-10">
                            <!-- Flash Message -->
                            <div v-if="props.flash?.success"
                                class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-700 dark:text-green-400 font-medium">
                                {{ props.flash.success }}
                            </div>

                            <article
                                class="bg-background dark:bg-background p-8 md:p-12 rounded-2xl shadow-sm border border-neutral-200/60 dark:border-neutral-800/60">
                                <header class="mb-8 border-b border-neutral-100 dark:border-neutral-800 pb-8">
                                    <p
                                        class="text-neutral-500 dark:text-neutral-400 text-sm font-medium tracking-wide uppercase mb-3">
                                        {{ formatFullDate(props.entry.date) }}</p>
                                    <h1 class="text-4xl md:text-5xl font-bold font-serif leading-tight wrap-break-word">
                                        {{ props.entry.title }}</h1>
                                </header>
                                <div class="prose-custom text-lg leading-relaxed text-neutral-700 dark:text-neutral-300 wrap-break-word"
                                    v-html="props.entry.body"></div>
                            </article>
                        </main>

                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Entry Modal (Duplicated layout for Show.vue) -->
        <Dialog v-model:open="isEditModalOpen">
            <DialogContent
                class="sm:max-w-2xl p-0 gap-0 overflow-hidden bg-background dark:bg-background font-display border border-neutral-200/60 dark:border-neutral-800/60">
                <div class="p-6 pb-4 border-b border-neutral-100 dark:border-neutral-800">
                    <DialogHeader>
                        <DialogTitle class="text-2xl font-bold font-serif flex items-center gap-2">
                            <svg class="size-6 text-neutral-700 dark:text-neutral-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                            </svg>
                            Edit Entry
                        </DialogTitle>
                    </DialogHeader>
                </div>

                <form @submit.prevent="submitEdit" class="flex flex-col h-full max-h-[80vh]">
                    <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-neutral-500 dark:text-neutral-400 text-sm font-bold tracking-wide uppercase px-1">Entry
                                    Title</label>
                                <input v-model="editForm.title" required
                                    class="w-full bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl text-xl font-bold placeholder:text-neutral-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
                                    type="text" />
                                <span v-if="editForm.errors.title" class="text-red-500 text-sm px-1">{{
                                    editForm.errors.title }}</span>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-neutral-500 dark:text-neutral-400 text-sm font-bold tracking-wide uppercase px-1">Date</label>
                                <input v-model="editForm.date" required type="date"
                                    class="w-full bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl text-base placeholder:text-neutral-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" />
                                <span v-if="editForm.errors.date" class="text-red-500 text-sm px-1">{{
                                    editForm.errors.date }}</span>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-neutral-500 dark:text-neutral-400 text-sm font-bold tracking-wide uppercase px-1">Journal
                                    Body</label>
                                <div
                                    class="flex flex-col rounded-xl bg-background dark:bg-background shadow-sm border border-neutral-200/60 dark:border-neutral-800/60 overflow-hidden">
                                    <div ref="editToolbarContainer"
                                        class="flex flex-wrap items-center justify-between border-b border-neutral-200 dark:border-neutral-800 px-3 py-2 bg-neutral-50 dark:bg-neutral-950">
                                        <div class="flex gap-0.5 items-center">
                                            <button type="button"
                                                class="ql-bold p-1.5 text-neutral-600 dark:text-neutral-300 hover:text-primary hover:bg-primary/10 rounded transition-colors"
                                                title="Bold"></button>
                                            <button type="button"
                                                class="ql-italic p-1.5 text-neutral-600 dark:text-neutral-300 hover:text-primary hover:bg-primary/10 rounded transition-colors"
                                                title="Italic"></button>
                                            <button type="button"
                                                class="ql-underline p-1.5 text-neutral-600 dark:text-neutral-300 hover:text-primary hover:bg-primary/10 rounded transition-colors"
                                                title="Underline"></button>
                                            <div class="w-px h-5 bg-neutral-200 dark:bg-neutral-700 mx-2 my-auto"></div>
                                            <button type="button"
                                                class="ql-list p-1.5 text-neutral-600 dark:text-neutral-300 hover:text-primary hover:bg-primary/10 rounded transition-colors"
                                                value="ordered" title="Numbered List"></button>
                                            <button type="button"
                                                class="ql-list p-1.5 text-neutral-600 dark:text-neutral-300 hover:text-primary hover:bg-primary/10 rounded transition-colors"
                                                value="bullet" title="Bulleted List"></button>
                                            <div class="w-px h-5 bg-neutral-200 dark:bg-neutral-700 mx-2 my-auto"></div>
                                            <button type="button"
                                                class="ql-blockquote p-1.5 text-neutral-600 dark:text-neutral-300 hover:text-primary hover:bg-primary/10 rounded transition-colors"
                                                title="Quote"></button>
                                            <button type="button"
                                                class="ql-link p-1.5 text-neutral-600 dark:text-neutral-300 hover:text-primary hover:bg-primary/10 rounded transition-colors"
                                                title="Link"></button>
                                        </div>
                                        <div class="text-xs text-neutral-400 font-medium px-2 hidden sm:block">
                                            <span>{{ editWordCount }} words</span> • <span>{{ editCharCount }}
                                                chars</span>
                                        </div>
                                    </div>
                                    <div ref="editEditorContainer"
                                        class="w-full min-h-[250px] p-5 bg-transparent resize-y focus:outline-none focus:ring-0 border-none text-base leading-relaxed placeholder:text-neutral-400 dark:placeholder:text-neutral-600 quill-editor-content">
                                    </div>
                                </div>
                                <span v-if="editForm.errors.body" class="text-red-500 text-sm px-1">{{
                                    editForm.errors.body }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 p-4 px-6 border-t border-neutral-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50 rounded-b-xl mt-auto">
                        <Button type="button" variant="outline" @click="isEditModalOpen = false"
                            class="font-medium">Cancel</Button>
                        <Button type="submit" :disabled="editForm.processing"
                            class="font-medium flex items-center gap-2 bg-neutral-900 text-white hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white">
                            <Save class="size-4" />
                            Update Changes
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>

<style>
.prose-custom p {
    margin-bottom: 1rem;
}

.prose-custom ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.prose-custom ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.prose-custom a {
    color: inherit;
    text-decoration: underline;
}

.prose-custom blockquote {
    border-left: 4px solid #020617;
    padding-left: 1rem;
    color: #64748b;
    font-style: italic;
    margin-bottom: 1rem;
}

/* Adjust Quill Editor to match styling */
.quill-editor-content.ql-container {
    font-family: 'Inter', sans-serif;
    font-size: 16px;
}

.ql-editor {
    padding: 0;
}
</style>
