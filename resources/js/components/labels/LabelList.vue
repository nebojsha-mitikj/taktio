<script setup lang="ts">
import LabelActions from '@/components/labels/LabelActions.vue';
import LabelName from '@/components/labels/LabelName.vue';
import type { Label } from '@/types/labels/Label';

const { labels } = defineProps<{ labels: Label[] }>();

const emit = defineEmits<{
    (e: 'edit', label: Label): void;
}>();
</script>

<template>
    <div
        v-if="labels.length"
        class="rounded-xl bg-card shadow-sm ring-1 ring-black/[0.07] ring-inset dark:ring-white/[0.07]"
    >
        <div
            v-for="label in labels"
            :key="label.id"
            class="flex items-center justify-between border-b border-black/[0.05] px-5 py-3.5 transition-colors duration-150 first:rounded-t-xl last:rounded-b-xl last:border-b-0 hover:bg-black/[0.025] dark:border-white/[0.05] dark:hover:bg-white/[0.025]"
        >
            <LabelName :id="label.id" :name="label.name" />
            <LabelActions @edit="emit('edit', $event)" :label="label" />
        </div>
    </div>

    <div
        v-else
        class="rounded-xl border border-black/[0.07] bg-card px-6 py-12 text-center shadow-sm dark:border-white/[0.07]"
    >
        <p class="text-[15px] font-semibold text-foreground">No labels yet</p>
        <p class="mt-1 text-sm text-muted-foreground">
            Create one to organize your tasks.
        </p>
    </div>
</template>
