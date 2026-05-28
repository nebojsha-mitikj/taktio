<script setup lang="ts">
import { destroy } from '@/actions/App/Http/Controllers/LabelController';
import ConfirmAlert from '@/components/ui-custom/ConfirmAlert.vue';
import type { Label } from '@/types/labels/Label';
import { router } from '@inertiajs/vue3';
import { ChevronRight, Ellipsis, Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const { label } = defineProps<{ label: Label }>();

const emit = defineEmits<{
    (e: 'edit', label: Label): void;
}>();

const open = ref(false);
const isDeleting = ref(false);
const showAlert = ref(false);

const deleteLabel = (): void => {
    isDeleting.value = true;
    router.delete(destroy(label.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Label deleted successfully.'),
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};
</script>

<template>
    <ConfirmAlert
        :request-is-active="isDeleting"
        description="This action cannot be undone. This will permanently delete your label from our servers."
        confirm-label="Delete"
        v-model:open="showAlert"
        @submit="deleteLabel"
    />

    <div class="relative" @mouseenter="open = true" @mouseleave="open = false">
        <button
            @click="open = !open"
            class="flex size-8 cursor-pointer items-center justify-center rounded-full transition-colors hover:bg-black/5 dark:hover:bg-white/5"
        >
            <Ellipsis class="size-4 text-muted-foreground" />
        </button>

        <div v-show="open" class="absolute top-full right-0 z-50 h-2 w-40" />

        <div
            v-show="open"
            class="absolute top-[calc(100%+0.5rem)] right-0 z-50 w-40 rounded-md border border-black/[0.08] bg-card py-1 shadow-md dark:border-white/[0.08]"
        >
            <button
                class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-muted-foreground transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                @click="
                    emit('edit', label);
                    open = false;
                "
            >
                <Pencil class="size-3.5" />
                Edit
            </button>
            <button
                class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-muted-foreground transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                @click="
                    router.visit('/labels/' + label.id);
                    open = false;
                "
            >
                <ChevronRight class="size-3.5" />
                View tasks
            </button>
            <div
                class="my-1 border-t border-black/[0.06] dark:border-white/[0.06]"
            />
            <button
                class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-950/30"
                @click="
                    showAlert = true;
                    open = false;
                "
            >
                <Trash2 class="size-3.5" />
                Delete
            </button>
        </div>
    </div>
</template>
