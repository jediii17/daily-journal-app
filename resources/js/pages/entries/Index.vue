<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { ref, watch, nextTick } from 'vue';
import 'quill/dist/quill.snow.css';
import Quill from 'quill';
import { BookOpen, Search, Plus, Save, History } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

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

// Search functionality
const searchForm = useForm({
    search: props.search || ''
});

// Submit search
const submitSearch = () => {
    router.get('/dashboard', { search: searchForm.search }, { preserveState: true });
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
        }
    });
};


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
    editCharCount.value = text.trim() === "" ? 0 : text.length - 1;
    editWordCount.value = text.trim() === "" ? 0 : text.trim().split(/\s+/).length;
};

// Initialize edit Quill editor
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
        }
    });
};
</script>

<template>

    <Head title="My Journal - Home" />

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
                                    <div
                                        class="size-6 flex items-center justify-center text-neutral-800 dark:text-neutral-100">
                                        <BookOpen class="size-6" />
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
                                        class="flex w-full flex-1 items-stretch rounded-full h-full bg-white dark:bg-neutral-900 shadow-sm border border-neutral-200 dark:border-neutral-700 focus-within:ring-2 focus-within:ring-neutral-900/10 dark:focus-within:ring-neutral-100/10 transition-all overflow-hidden">
                                        <button type="submit"
                                            class="text-neutral-400 dark:text-neutral-500 hover:text-neutral-600 dark:hover:text-neutral-300 transition-colors flex items-center justify-center pl-4 pr-2 h-full">
                                            <Search class="size-5" />
                                        </button>
                                        <input v-model="searchForm.search" placeholder="Search entries..." 
                                            class="flex-1 bg-transparent border-none focus:outline-none focus:ring-0 px-2 py-0 text-sm text-neutral-800 dark:text-neutral-200 placeholder:text-neutral-400 dark:placeholder:text-neutral-500 h-full w-full" />
                                        <button type="button" @click="() => { searchForm.search = ''; submitSearch(); }" v-if="searchForm.search"
                                            class="text-neutral-400 dark:text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200 transition-colors flex items-center justify-center pr-4 pl-2 h-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                        </button>
                                    </div>
                                </form>
                                <Button @click="openCreateModal"
                                    class="rounded-full shadow-sm font-semibold h-10 px-5 flex items-center gap-2 bg-neutral-900 text-white hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white">
                                    <Plus class="size-4" />
                                    New Entry
                                </Button>
                            </div>
                        </header>

                        <main class="flex-1 py-8 px-4 md:px-10">

                            <!-- Flash Message -->
                            <div v-if="props.flash?.success"
                                class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-700 dark:text-green-400 font-medium">
                                {{ props.flash.success }}
                            </div>

                            <!-- List Entries Header -->
                            <div class="flex items-center justify-between mb-6">
                                <h3
                                    class="text-xl font-bold flex items-center gap-2 font-serif text-neutral-800 dark:text-neutral-100">
                                    <History class="size-6 text-neutral-700 dark:text-neutral-200" />
                                    <span v-if="props.search">Search Results for "{{ props.search }}"</span>
                                    <span v-else>Past Entries</span>
                                </h3>
                            </div>

                            <p v-if="props.entries.length === 0"
                                class="text-neutral-500 dark:text-neutral-400 italic bg-background dark:bg-background p-8 rounded-xl border border-neutral-200/60 dark:border-neutral-800/60 text-center">
                                <span v-if="props.search">No entries found matching your search.</span>
                                <span v-else>No entries yet. Start writing your first journal!</span>
                            </p>

                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <a v-for="entry in props.entries" :key="entry.id"
                                    class="relative flex flex-col p-6 rounded-2xl bg-background dark:bg-background shadow-sm border border-neutral-200/60 dark:border-neutral-800/60 hover:border-neutral-400 dark:hover:border-neutral-600 hover:shadow-md transition-all group flex-1 cursor-pointer"
                                    :href="'/entries/' + entry.id">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4
                                            class="font-bold text-[1.15rem] leading-tight text-neutral-800 dark:text-neutral-100 group-hover:text-neutral-900 dark:group-hover:text-neutral-50 transition-colors pr-10">
                                            {{ entry.title }}</h4>
                                        <span
                                            class="text-xs font-semibold text-neutral-500 bg-neutral-100 dark:bg-neutral-800 dark:text-neutral-400 rounded-full px-2.5 py-1 whitespace-nowrap">{{
                                                formatDate(entry.date) }}</span>
                                    </div>
                                    <p
                                        class="text-[0.95rem] text-neutral-600 dark:text-neutral-400 flex-1 line-clamp-3 leading-relaxed">
                                        {{ stripTagsAndLimit(entry.body, 120) }}</p>

                                    <!-- Edit Quick Action -->
                                    <Button @click="(e: MouseEvent) => openEditModal(entry, e)" aria-label="Edit Entry"
                                        class="absolute top-4 right-4 p-2 rounded-full opacity-0 tranneutral-y-2 group-hover:opacity-100 group-hover:tranneutral-y-0 transition-all hover:bg-neutral-100 dark:hover:bg-neutral-800 bg-white dark:bg-neutral-900 shadow-sm border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 pointer-events-auto">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
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
                class="sm:max-w-2xl p-0 gap-0 overflow-hidden bg-background dark:bg-background font-display border border-neutral-200/60 dark:border-neutral-800/60">
                <div class="p-6 pb-4 border-b border-neutral-100 dark:border-neutral-800">
                    <DialogHeader>
                        <DialogTitle class="text-2xl font-bold font-serif flex items-center gap-2">
                            <Plus class="size-6 text-primary" />
                            Create New Entry
                        </DialogTitle>
                    </DialogHeader>
                </div>

                <form @submit.prevent="submitCreate" class="flex flex-col h-full max-h-[80vh]">
                    <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-neutral-500 dark:text-neutral-400 text-sm font-bold tracking-wide uppercase px-1">Entry
                                    Title</label>
                                <Input v-model="form.title" required
                                    class="w-full bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl text-xl font-bold placeholder:text-neutral-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
                                    placeholder="What's on your mind?" type="text" />
                                <span v-if="form.errors.title" class="text-red-500 text-sm px-1">{{ form.errors.title
                                }}</span>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-neutral-500 dark:text-neutral-400 text-sm font-bold tracking-wide uppercase px-1">Date</label>
                                <Input v-model="form.date" required type="date"
                                    class="w-full bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl text-base placeholder:text-neutral-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all" />
                                <span v-if="form.errors.date" class="text-red-500 text-sm px-1">{{ form.errors.date
                                }}</span>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-neutral-500 dark:text-neutral-400 text-sm font-bold tracking-wide uppercase px-1">Journal
                                    Body</label>
                                <div
                                    class="flex flex-col rounded-xl bg-background dark:bg-background shadow-sm border border-neutral-200/60 dark:border-neutral-800/60 overflow-hidden">
                                    <div ref="toolbarContainer"
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
                                            <span>{{ wordCount }} words</span> • <span>{{ charCount }} chars</span>
                                        </div>
                                    </div>
                                    <!-- Give it a specific min-height so it looks good inside a modal -->
                                    <div ref="editorContainer"
                                        class="w-full min-h-[250px] p-5 bg-transparent resize-y focus:outline-none focus:ring-0 border-none text-base leading-relaxed placeholder:text-neutral-400 dark:placeholder:text-neutral-600 quill-editor-content">
                                    </div>
                                </div>
                                <span v-if="form.errors.body" class="text-red-500 text-sm px-1">{{ form.errors.body
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 p-4 px-6 border-t border-neutral-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50 rounded-b-xl mt-auto">
                        <Button type="button" variant="outline" @click="isCreateModalOpen = false"
                            class="font-medium">Cancel</Button>
                        <Button type="submit" :disabled="form.processing"
                            class="font-medium flex items-center gap-2 bg-neutral-900 text-white hover:bg-black dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-white">
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
                class="sm:max-w-2xl p-0 gap-0 overflow-hidden bg-background dark:bg-background font-display border border-neutral-200/60 dark:border-neutral-800/60">
                <div class="p-6 pb-4 border-b border-neutral-100 dark:border-neutral-800">
                    <DialogHeader>
                        <DialogTitle class="text-2xl font-bold font-serif flex items-center gap-2">
                            <svg class="size-6 text-primary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
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
                                <Input v-model="editForm.title" required
                                    class="w-full bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl text-xl font-bold placeholder:text-neutral-400 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
                                    type="text" />
                                <span v-if="editForm.errors.title" class="text-red-500 text-sm px-1">{{
                                    editForm.errors.title }}</span>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-neutral-500 dark:text-neutral-400 text-sm font-bold tracking-wide uppercase px-1">Date</label>
                                <Input v-model="editForm.date" required type="date"
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
