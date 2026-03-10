<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Edit3, Save } from 'lucide-vue-next';
import Quill from 'quill';
import { nextTick, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import 'quill/dist/quill.snow.css';

const props = defineProps<{
    entry: any;
    flash?: {
        success?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'My Journal', href: '/dashboard' },
];

const formatFullDate = (dateString: string) => {
    const options: Intl.DateTimeFormatOptions = {
        weekday: 'long',
        month: 'long',
        day: '2-digit',
        year: 'numeric',
    };
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
    editCharCount.value = text.trim() === '' ? 0 : text.length - 1;
    editWordCount.value =
        text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
};

const initEditQuill = () => {
    if (editEditorContainer.value && editToolbarContainer.value && !editQuill) {
        editQuill = new Quill(editEditorContainer.value, {
            theme: 'snow',
            placeholder: 'Edit your thoughts...',
            modules: {
                toolbar: editToolbarContainer.value,
            },
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
        },
    });
};
</script>

<template>
    <Head :title="props.entry.title + ' - My Journal'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="group/design-root font-display relative flex h-auto min-h-screen w-full flex-col overflow-x-hidden text-neutral-900 dark:text-neutral-100"
        >
            <div class="layout-container flex h-full grow flex-col">
                <div
                    class="flex flex-1 justify-center px-4 py-5 md:px-10 lg:px-40"
                >
                    <div
                        class="layout-content-container flex max-w-[960px] flex-1 flex-col"
                    >
                        <!-- Header Area -->
                        <header
                            class="flex flex-col items-center justify-between gap-4 border-b border-solid border-neutral-200 px-4 py-3 whitespace-nowrap md:flex-row md:gap-0 md:px-10 dark:border-neutral-800"
                        >
                            <div
                                class="flex w-full items-center justify-between gap-8 md:w-auto md:justify-start"
                            >
                                <div class="flex items-center gap-4">
                                    <Link
                                        href="/dashboard"
                                        class="flex size-6 items-center justify-center text-neutral-700 transition-opacity hover:opacity-80 dark:text-neutral-200"
                                    >
                                        <ArrowLeft class="size-6" />
                                    </Link>
                                    <Link
                                        href="/dashboard"
                                        class="font-serif text-xl leading-tight font-bold tracking-[-0.015em]"
                                        >My Journal</Link
                                    >
                                </div>
                            </div>

                            <!-- Edit Button -->
                            <div
                                class="flex w-full flex-1 items-center justify-end md:w-auto"
                            >
                                <Button
                                    @click="openEditModal"
                                    variant="outline"
                                    class="flex h-9 items-center gap-2 rounded-full bg-neutral-900 px-4 font-semibold text-white shadow-sm hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white"
                                >
                                    <Edit3 class="size-4" />
                                    Edit Entry
                                </Button>
                            </div>
                        </header>

                        <main class="flex-1 px-4 py-12 md:px-10">
                            <!-- Flash Message -->
                            <div
                                v-if="props.flash?.success"
                                class="mb-6 rounded-lg border border-green-500/20 bg-green-500/10 p-4 font-medium text-green-700 dark:text-green-400"
                            >
                                {{ props.flash.success }}
                            </div>

                            <article
                                class="rounded-2xl border border-neutral-200/60 bg-background p-8 shadow-sm md:p-12 dark:border-neutral-800/60 dark:bg-background"
                            >
                                <header
                                    class="mb-8 border-b border-neutral-100 pb-8 dark:border-neutral-800"
                                >
                                    <p
                                        class="mb-3 text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400"
                                    >
                                        {{ formatFullDate(props.entry.date) }}
                                    </p>
                                    <h1
                                        class="font-serif text-4xl leading-tight font-bold wrap-break-word md:text-5xl"
                                    >
                                        {{ props.entry.title }}
                                    </h1>
                                </header>
                                <div
                                    class="prose-custom text-lg leading-relaxed wrap-break-word text-neutral-700 dark:text-neutral-300"
                                    v-html="props.entry.body"
                                ></div>
                            </article>
                        </main>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Entry Modal (Duplicated layout for Show.vue) -->
        <Dialog v-model:open="isEditModalOpen">
            <DialogContent
                class="font-display gap-0 overflow-hidden border border-neutral-200/60 bg-background p-0 sm:max-w-2xl dark:border-neutral-800/60 dark:bg-background"
            >
                <div
                    class="border-b border-neutral-100 p-6 pb-4 dark:border-neutral-800"
                >
                    <DialogHeader>
                        <DialogTitle
                            class="flex items-center gap-2 font-serif text-2xl font-bold"
                        >
                            <svg
                                class="size-6 text-neutral-700 dark:text-neutral-200"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M12 20h9" />
                                <path
                                    d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"
                                />
                            </svg>
                            Edit Entry
                        </DialogTitle>
                    </DialogHeader>
                </div>

                <form
                    @submit.prevent="submitEdit"
                    class="flex h-full max-h-[80vh] flex-col"
                >
                    <div class="custom-scrollbar flex-1 overflow-y-auto p-6">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="px-1 text-sm font-bold tracking-wide text-neutral-500 uppercase dark:text-neutral-400"
                                    >Entry Title</label
                                >
                                <input
                                    v-model="editForm.title"
                                    required
                                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-xl font-bold transition-all placeholder:text-neutral-400 focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900"
                                    type="text"
                                />
                                <span
                                    v-if="editForm.errors.title"
                                    class="px-1 text-sm text-red-500"
                                    >{{ editForm.errors.title }}</span
                                >
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="px-1 text-sm font-bold tracking-wide text-neutral-500 uppercase dark:text-neutral-400"
                                    >Date</label
                                >
                                <input
                                    v-model="editForm.date"
                                    required
                                    type="date"
                                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-base transition-all placeholder:text-neutral-400 focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900"
                                />
                                <span
                                    v-if="editForm.errors.date"
                                    class="px-1 text-sm text-red-500"
                                    >{{ editForm.errors.date }}</span
                                >
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="px-1 text-sm font-bold tracking-wide text-neutral-500 uppercase dark:text-neutral-400"
                                    >Journal Body</label
                                >
                                <div
                                    class="flex flex-col overflow-hidden rounded-xl border border-neutral-200/60 bg-background shadow-sm dark:border-neutral-800/60 dark:bg-background"
                                >
                                    <div
                                        ref="editToolbarContainer"
                                        class="flex flex-wrap items-center justify-between border-b border-neutral-200 bg-neutral-50 px-3 py-2 dark:border-neutral-800 dark:bg-neutral-950"
                                    >
                                        <div class="flex items-center gap-0.5">
                                            <button
                                                type="button"
                                                class="ql-bold rounded p-1.5 text-neutral-600 transition-colors hover:bg-primary/10 hover:text-primary dark:text-neutral-300"
                                                title="Bold"
                                            ></button>
                                            <button
                                                type="button"
                                                class="ql-italic rounded p-1.5 text-neutral-600 transition-colors hover:bg-primary/10 hover:text-primary dark:text-neutral-300"
                                                title="Italic"
                                            ></button>
                                            <button
                                                type="button"
                                                class="ql-underline rounded p-1.5 text-neutral-600 transition-colors hover:bg-primary/10 hover:text-primary dark:text-neutral-300"
                                                title="Underline"
                                            ></button>
                                            <div
                                                class="mx-2 my-auto h-5 w-px bg-neutral-200 dark:bg-neutral-700"
                                            ></div>
                                            <button
                                                type="button"
                                                class="ql-list rounded p-1.5 text-neutral-600 transition-colors hover:bg-primary/10 hover:text-primary dark:text-neutral-300"
                                                value="ordered"
                                                title="Numbered List"
                                            ></button>
                                            <button
                                                type="button"
                                                class="ql-list rounded p-1.5 text-neutral-600 transition-colors hover:bg-primary/10 hover:text-primary dark:text-neutral-300"
                                                value="bullet"
                                                title="Bulleted List"
                                            ></button>
                                            <div
                                                class="mx-2 my-auto h-5 w-px bg-neutral-200 dark:bg-neutral-700"
                                            ></div>
                                            <button
                                                type="button"
                                                class="ql-blockquote rounded p-1.5 text-neutral-600 transition-colors hover:bg-primary/10 hover:text-primary dark:text-neutral-300"
                                                title="Quote"
                                            ></button>
                                            <button
                                                type="button"
                                                class="ql-link rounded p-1.5 text-neutral-600 transition-colors hover:bg-primary/10 hover:text-primary dark:text-neutral-300"
                                                title="Link"
                                            ></button>
                                        </div>
                                        <div
                                            class="hidden px-2 text-xs font-medium text-neutral-400 sm:block"
                                        >
                                            <span
                                                >{{ editWordCount }} words</span
                                            >
                                            •
                                            <span
                                                >{{ editCharCount }} chars</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        ref="editEditorContainer"
                                        class="quill-editor-content min-h-[250px] w-full resize-y border-none bg-transparent p-5 text-base leading-relaxed placeholder:text-neutral-400 focus:ring-0 focus:outline-none dark:placeholder:text-neutral-600"
                                    ></div>
                                </div>
                                <span
                                    v-if="editForm.errors.body"
                                    class="px-1 text-sm text-red-500"
                                    >{{ editForm.errors.body }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-auto flex items-center justify-end gap-3 rounded-b-xl border-t border-neutral-100 bg-neutral-50 p-4 px-6 dark:border-neutral-800 dark:bg-neutral-900/50"
                    >
                        <Button
                            type="button"
                            variant="outline"
                            @click="isEditModalOpen = false"
                            class="font-medium"
                            >Cancel</Button
                        >
                        <Button
                            type="submit"
                            :disabled="editForm.processing"
                            class="flex items-center gap-2 bg-neutral-900 font-medium text-white hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white"
                        >
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
