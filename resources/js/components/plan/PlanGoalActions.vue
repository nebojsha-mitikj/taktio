<script setup lang="ts">
import { destroy as destroyGoal } from '@/actions/App/Http/Controllers/PlanGoalController';
import type { PlanGoal } from '@/types/plan/Plan';
import { router } from '@inertiajs/vue3';
import { Ellipsis, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const { goal } = defineProps<{
    goal: PlanGoal;
}>();

const emit = defineEmits<{
    (e: 'edit'): void;
    (e: 'add-step'): void;
}>();

const open = ref(false);
const isDeleting = ref(false);

let touchHandled = false;

const onEllipsisTouch = (): void => {
    touchHandled = true;
    open.value = !open.value;
};

const onEllipsisClick = (): void => {
    if (touchHandled) { touchHandled = false; return; }
    open.value = !open.value;
};

const onDelete = (): void => {
    isDeleting.value = true;
    router.delete(destroyGoal(goal.id).url, {
        preserveScroll: true,
        onFinish: () => { isDeleting.value = false; },
    });
};
</script>

<template>
    <div
        class="relative"
        @mouseenter="open = true"
        @mouseleave="open = false"
        @click.stop
    >
        <button
            class="flex size-7 cursor-pointer items-center justify-center rounded-full transition-colors hover:bg-black/5 dark:hover:bg-white/5"
            @touchstart.passive="onEllipsisTouch"
            @click="onEllipsisClick"
        >
            <Ellipsis class="size-4 text-[#aaa] dark:text-[#555]" />
        </button>

        <div
            v-show="open"
            class="absolute right-0 top-full z-50 w-36 pt-2"
        >
            <div class="rounded-md border border-black/[0.08] bg-white py-1 shadow-md dark:border-white/[0.08] dark:bg-[#111]">
                <button
                    class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-[#555] transition-colors hover:bg-black/5 dark:text-[#999] dark:hover:bg-white/5"
                    @click="emit('edit'); open = false"
                >
                    <Pencil class="size-3.5" />
                    Edit
                </button>
                <button
                    class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-[#555] transition-colors hover:bg-black/5 dark:text-[#999] dark:hover:bg-white/5"
                    @click="emit('add-step'); open = false"
                >
                    <Plus class="size-3.5" />
                    Add step
                </button>
                <div class="my-1 border-t border-black/[0.06] dark:border-white/[0.06]" />
                <button
                    class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-sm text-red-600 transition-colors hover:bg-red-50 disabled:opacity-50 dark:hover:bg-red-950/30"
                    :disabled="isDeleting"
                    @click="onDelete"
                >
                    <Trash2 class="size-3.5" />
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>
