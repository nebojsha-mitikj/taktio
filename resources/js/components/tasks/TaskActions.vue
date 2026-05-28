<script setup lang="ts">
import {
    destroy,
    updateLabels,
} from '@/actions/App/Http/Controllers/TaskController';
import ConfirmAlert from '@/components/ui-custom/ConfirmAlert.vue';
import type { Label } from '@/types/labels/Label';
import type { Task } from '@/types/tasks/Task';
import { router } from '@inertiajs/vue3';
import {
    Check,
    ChevronRight,
    Ellipsis,
    Pencil,
    Tag,
    Trash2,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const emit = defineEmits<{
    (e: 'edit', task: Task): void;
}>();

const { task, labels } = defineProps<{
    task: Task;
    labels: Label[];
}>();

const open = ref(false);
const labelsOpen = ref(false);

let touchHandledOpen = false;

const onOpenMouseEnter = (): void => {
    if (touchHandledOpen) return;
    open.value = true;
};
const onEllipsisTouch = (): void => {
    touchHandledOpen = true;
    open.value = !open.value;
};
const onEllipsisClick = (): void => {
    if (touchHandledOpen) {
        touchHandledOpen = false;
        return;
    }
    open.value = !open.value;
};

const isDeleting = ref(false);
const showDeleteAlert = ref(false);

const selectedLabelIds = ref<number[]>((task.labels ?? []).map((l) => l.id));

watch(
    () => task.labels,
    (labels) => {
        selectedLabelIds.value = (labels ?? []).map((l) => l.id);
    },
);

const toggleLabel = (labelId: number): void => {
    const ids = selectedLabelIds.value.includes(labelId)
        ? selectedLabelIds.value.filter((id) => id !== labelId)
        : [...selectedLabelIds.value, labelId];
    selectedLabelIds.value = ids;
    router.put(
        updateLabels(task.id),
        { label_ids: ids },
        { preserveScroll: true, preserveState: true },
    );
};

const onDelete = (): void => {
    isDeleting.value = true;
    router.delete(destroy(task.id), {
        preserveScroll: true,
        onSuccess: () => toast.success('Deleted successfully.'),
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};
</script>

<template>
    <ConfirmAlert
        :request-is-active="isDeleting"
        description="This action cannot be undone. This will permanently delete it from our servers."
        confirm-label="Delete"
        v-model:open="showDeleteAlert"
        @submit="onDelete"
    />

    <div
        class="relative"
        @mouseenter="onOpenMouseEnter"
        @mouseleave="
            open = false;
            labelsOpen = false;
        "
    >
        <button
            @touchstart.passive="onEllipsisTouch"
            @click="onEllipsisClick"
            class="flex size-8 cursor-pointer items-center justify-center rounded-full transition-colors hover:bg-black/5 dark:hover:bg-white/5"
        >
            <Ellipsis class="size-4 text-muted-foreground" />
        </button>

        <div v-show="open" class="absolute top-full right-0 z-50 h-2 w-48" />

        <div
            v-show="open"
            class="absolute top-[calc(100%+0.5rem)] right-0 z-50 w-48 rounded-md border border-black/[0.08] bg-card py-1 shadow-md dark:border-white/[0.08]"
        >
            <button
                class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-muted-foreground transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                @click="
                    emit('edit', task);
                    open = false;
                "
            >
                <Pencil class="size-3.5" />
                Edit
            </button>

            <template v-if="!task.is_virtual">
                <div
                    class="relative"
                    @mouseenter="labelsOpen = true"
                    @mouseleave="labelsOpen = false"
                >
                    <button
                        class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-muted-foreground transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    >
                        <Tag class="size-3.5" />
                        Labels
                        <ChevronRight class="ml-auto size-3" />
                    </button>

                    <div
                        v-show="labelsOpen"
                        class="absolute top-0 right-full z-50 w-48 rounded-md border border-black/[0.08] bg-card py-1 shadow-md dark:border-white/[0.08]"
                    >
                        <button
                            v-for="label in labels"
                            :key="label.id"
                            class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-muted-foreground transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                            @click="toggleLabel(label.id)"
                        >
                            <Check
                                v-if="selectedLabelIds.includes(label.id)"
                                class="size-3.5"
                            />
                            <span v-else class="size-3.5" />
                            {{ label.name }}
                        </button>
                        <span
                            v-if="!labels.length"
                            class="block px-3 py-1.5 text-sm text-muted-foreground"
                            >No labels</span
                        >
                    </div>
                </div>

                <div
                    class="my-1 border-t border-black/[0.06] dark:border-white/[0.06]"
                />

                <button
                    class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-950/30"
                    @click="
                        showDeleteAlert = true;
                        open = false;
                    "
                >
                    <Trash2 class="size-3.5" />
                    Delete
                </button>
            </template>
        </div>
    </div>
</template>
