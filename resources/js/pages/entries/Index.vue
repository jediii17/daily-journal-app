<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { BookOpen, History, Plus, Save, Search } from 'lucide-vue-next';
import Quill from 'quill';
import { nextTick, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import 'quill/dist/quill.snow.css';

const props = defineProps<{
    entries: any[];
    search?: string;
    flash?: {
        success?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'My Journal', href: '/dashboard' },
];

const form = useForm({
    title: '',
    date: new Date().toISOString().split('T')[0],
    body: '',
});

// Search functionality
const searchForm = useForm({
    search: props.search || '',
});

// Submit search
const submitSearch = () => {
    router.get(
        '/dashboard',
        { search: searchForm.search },
        { preserveState: true },
    );
};

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
    charCount.value = text.trim() === '' ? 0 : text.length - 1;
    wordCount.value = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
};

// Initialize Quill editor
const initQuill = () => {
    if (editorContainer.value && toolbarContainer.value && !quill) {
        quill = new Quill(editorContainer.value, {
            theme: 'snow',
            placeholder: 'Write your thoughts here...',
            modules: {
                toolbar: toolbarContainer.value,
            },
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

// Open create entry modal
const openCreateModal = () => {
    form.reset();
    form.date = new Date().toISOString().split('T')[0];
    isCreateModalOpen.value = true;
};

// Submit create entry
const submitCreate = () => {
    if (quill && quill.getText().trim() === '') {
        form.body = '';
    }
    form.post('/entries', {
        onSuccess: () => {
            isCreateModalOpen.value = false;
        },
    });
};

// Format date
const formatDate = (dateString: string) => {
    const options: Intl.DateTimeFormatOptions = {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
    };
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

// Edit functionality from index page
const isEditModalOpen = ref(false);
const editingId = ref<number | null>(null);

const editForm = useForm({
    title: '',
    date: '',
    body: '',
});

// Edit editor functionality
const editEditorContainer = ref<HTMLElement | null>(null);
const editToolbarContainer = ref<HTMLElement | null>(null);
let editQuill: Quill | null = null;
const editWordCount = ref(0);
const editCharCount = ref(0);

// Initialize edit Quill editor
// Update edit word and character counts
const updateEditCounts = () => {
    if (!editQuill) return;
    const text = editQuill.getText();
    editCharCount.value = text.trim() === '' ? 0 : text.length - 1;
    editWordCount.value =
        text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
};

// Initialize edit Quill editor
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

// Watch edit modal open state
watch(isEditModalOpen, async (isOpen) => {
    if (isOpen) {
        await nextTick();
        initEditQuill();
    } else {
        editQuill = null;
        editingId.value = null;
    }
});

// Open edit modal
const openEditModal = (entry: any, e: Event) => {
    e.stopPropagation();
    e.preventDefault();
    editingId.value = entry.id;
    editForm.title = entry.title;
    editForm.date = entry.date.split('T')[0];
    editForm.body = entry.body;
    isEditModalOpen.value = true;
};

// Submit edit
const submitEdit = () => {
    if (editQuill && editQuill.getText().trim() === '') {
        editForm.body = '';
    }
    editForm.patch(`/entries/${editingId.value}`, {
        onSuccess: () => {
            isEditModalOpen.value = false;
        },
    });
};
</script>

<template>
    <Head title="My Journal - Home" />

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
                                    <div
                                        class="flex size-6 items-center justify-center text-neutral-800 dark:text-neutral-100"
                                    >
                                        <BookOpen class="size-6" />
                                    </div>
                                    <a
                                        href="/dashboard"
                                        class="font-serif text-xl leading-tight font-bold tracking-[-0.015em]"
                                        >My Journal</a
                                    >
                                </div>
                            </div>
                            <div
                                class="flex w-full flex-1 items-center justify-end gap-4 md:w-auto md:gap-6"
                            >
                                <form
                                    @submit.prevent="submitSearch"
                                    class="flex h-10 w-full max-w-64 min-w-40 flex-col md:w-auto"
                                >
                                    <div
                                        class="flex h-full w-full flex-1 items-stretch overflow-hidden rounded-full border border-neutral-200 bg-white shadow-sm transition-all focus-within:ring-2 focus-within:ring-neutral-900/10 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-within:ring-neutral-100/10"
                                    >
                                        <button
                                            type="submit"
                                            class="flex h-full items-center justify-center pr-2 pl-4 text-neutral-400 transition-colors hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300"
                                        >
                                            <Search class="size-5" />
                                        </button>
                                        <input
                                            v-model="searchForm.search"
                                            placeholder="Search entries..."
                                            class="h-full w-full flex-1 border-none bg-transparent px-2 py-0 text-sm text-neutral-800 placeholder:text-neutral-400 focus:ring-0 focus:outline-none dark:text-neutral-200 dark:placeholder:text-neutral-500"
                                        />
                                        <button
                                            type="button"
                                            @click="
                                                () => {
                                                    searchForm.search = '';
                                                    submitSearch();
                                                }
                                            "
                                            v-if="searchForm.search"
                                            class="flex h-full items-center justify-center pr-4 pl-2 text-neutral-400 transition-colors hover:text-neutral-800 dark:text-neutral-500 dark:hover:text-neutral-200"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="size-4"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <path d="M18 6 6 18" />
                                                <path d="m6 6 12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                                <Button
                                    @click="openCreateModal"
                                    class="flex h-10 items-center gap-2 rounded-full bg-neutral-900 px-5 font-semibold text-white shadow-sm hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white"
                                >
                                    <Plus class="size-4" />
                                    New Entry
                                </Button>
                            </div>
                        </header>

                        <main class="flex-1 px-4 py-8 md:px-10">
                            <!-- Flash Message -->
                            <div
                                v-if="props.flash?.success"
                                class="mb-6 rounded-lg border border-green-500/20 bg-green-500/10 p-4 font-medium text-green-700 dark:text-green-400"
                            >
                                {{ props.flash.success }}
                            </div>

                            <!-- List Entries Header -->
                            <div class="mb-6 flex items-center justify-between">
                                <h3
                                    class="flex items-center gap-2 font-serif text-xl font-bold text-neutral-800 dark:text-neutral-100"
                                >
                                    <History
                                        class="size-6 text-neutral-700 dark:text-neutral-200"
                                    />
                                    <span v-if="props.search"
                                        >Search Results for "{{
                                            props.search
                                        }}"</span
                                    >
                                    <span v-else>Past Entries</span>
                                </h3>
                            </div>

                            <p
                                v-if="props.entries.length === 0"
                                class="rounded-xl border border-neutral-200/60 bg-background p-8 text-center text-neutral-500 italic dark:border-neutral-800/60 dark:bg-background dark:text-neutral-400"
                            >
                                <span v-if="props.search"
                                    >No entries found matching your
                                    search.</span
                                >
                                <span v-else
                                    >No entries yet. Start writing your first
                                    journal!</span
                                >
                            </p>

                            <div
                                v-else
                                class="grid grid-cols-1 gap-5 md:grid-cols-2"
                            >
                                <a
                                    v-for="entry in props.entries"
                                    :key="entry.id"
                                    class="group relative flex flex-1 cursor-pointer flex-col rounded-2xl border border-neutral-200/60 bg-background p-6 shadow-sm transition-all hover:border-neutral-400 hover:shadow-md dark:border-neutral-800/60 dark:bg-background dark:hover:border-neutral-600"
                                    :href="'/entries/' + entry.id"
                                >
                                    <div
                                        class="mb-3 flex items-start justify-between"
                                    >
                                        <h4
                                            class="pr-10 text-[1.15rem] leading-tight font-bold text-neutral-800 transition-colors group-hover:text-neutral-900 dark:text-neutral-100 dark:group-hover:text-neutral-50"
                                        >
                                            {{ entry.title }}
                                        </h4>
                                        <span
                                            class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-semibold whitespace-nowrap text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400"
                                            >{{ formatDate(entry.date) }}</span
                                        >
                                    </div>
                                    <p
                                        class="line-clamp-3 flex-1 text-[0.95rem] leading-relaxed text-neutral-600 dark:text-neutral-400"
                                    >
                                        {{ stripTagsAndLimit(entry.body, 120) }}
                                    </p>

                                    <!-- Edit Quick Action -->
                                    <Button
                                        @click="
                                            (e: MouseEvent) =>
                                                openEditModal(entry, e)
                                        "
                                        aria-label="Edit Entry"
                                        class="tranneutral-y-2 group-hover:tranneutral-y-0 pointer-events-auto absolute top-4 right-4 rounded-full border border-neutral-200 bg-white p-2 text-neutral-600 opacity-0 shadow-sm transition-all group-hover:opacity-100 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800"
                                    >
                                        <svg
                                            class="size-4"
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
                                    </Button>
                                </a>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Entry Modal -->
        <Dialog v-model:open="isCreateModalOpen">
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
                            <Plus class="size-6 text-primary" />
                            Create New Entry
                        </DialogTitle>
                    </DialogHeader>
                </div>

                <form
                    @submit.prevent="submitCreate"
                    class="flex h-full max-h-[80vh] flex-col"
                >
                    <div class="custom-scrollbar flex-1 overflow-y-auto p-6">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="px-1 text-sm font-bold tracking-wide text-neutral-500 uppercase dark:text-neutral-400"
                                    >Entry Title</label
                                >
                                <Input
                                    v-model="form.title"
                                    required
                                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-xl font-bold transition-all placeholder:text-neutral-400 focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900"
                                    placeholder="What's on your mind?"
                                    type="text"
                                />
                                <span
                                    v-if="form.errors.title"
                                    class="px-1 text-sm text-red-500"
                                    >{{ form.errors.title }}</span
                                >
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="px-1 text-sm font-bold tracking-wide text-neutral-500 uppercase dark:text-neutral-400"
                                    >Date</label
                                >
                                <Input
                                    v-model="form.date"
                                    required
                                    type="date"
                                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-base transition-all placeholder:text-neutral-400 focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-neutral-800 dark:bg-neutral-900"
                                />
                                <span
                                    v-if="form.errors.date"
                                    class="px-1 text-sm text-red-500"
                                    >{{ form.errors.date }}</span
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
                                        ref="toolbarContainer"
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
                                            <span>{{ wordCount }} words</span> •
                                            <span>{{ charCount }} chars</span>
                                        </div>
                                    </div>
                                    <!-- Give it a specific min-height so it looks good inside a modal -->
                                    <div
                                        ref="editorContainer"
                                        class="quill-editor-content min-h-[250px] w-full resize-y border-none bg-transparent p-5 text-base leading-relaxed placeholder:text-neutral-400 focus:ring-0 focus:outline-none dark:placeholder:text-neutral-600"
                                    ></div>
                                </div>
                                <span
                                    v-if="form.errors.body"
                                    class="px-1 text-sm text-red-500"
                                    >{{ form.errors.body }}</span
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
                            @click="isCreateModalOpen = false"
                            class="font-medium"
                            >Cancel</Button
                        >
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center gap-2 bg-neutral-900 font-medium text-white hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white"
                        >
                            <Save class="size-4" />
                            Save Journal
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Edit Entry Modal -->
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
                                class="size-6 text-primary"
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
                                <Input
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
                                <Input
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
/* Adjust Quill Editor to match styling */
.quill-editor-content.ql-container {
    font-family: 'Inter', sans-serif;
    font-size: 16px;
}

.ql-editor {
    padding: 0;
}

.dark .ql-editor.ql-blank::before {
    color: rgb(239, 239, 239);
}
</style>
