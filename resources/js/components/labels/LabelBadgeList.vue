<script setup lang="ts">
import { show } from '@/routes/labels';
import { Label } from '@/types/labels/Label';
import { Link } from '@inertiajs/vue3';

const { labels } = defineProps<{
    labels: Label[];
}>();
</script>

<template>
    <template v-if="labels.length > 0">
        <Link
            v-for="label in labels"
            :key="label.id"
            :href="show(label.id)"
            class="inline-flex cursor-pointer items-center rounded bg-black/[0.06] px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground transition-opacity hover:opacity-75 dark:bg-white/[0.06]"
        >
            {{ label.name }}
        </Link>
    </template>
</template>
