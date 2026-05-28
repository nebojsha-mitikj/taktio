<script setup lang="ts" generic="T extends { id: number }">
const { items, emptyText } = defineProps<{
    items: T[];
    emptyText: string;
}>();
</script>

<template>
    <div>
        <div
            v-if="items.length"
            class="rounded-xl bg-card shadow-sm ring-1 ring-inset ring-black/[0.07] dark:ring-white/[0.07]"
        >
            <div
                v-for="item in items"
                :key="item.id"
                class="flex items-start justify-between gap-4 border-b border-black/[0.05] px-5 py-4 transition-colors duration-150 first:rounded-t-xl last:rounded-b-xl last:border-b-0 hover:bg-black/[0.02] sm:px-6 dark:border-white/[0.05] dark:hover:bg-white/[0.02]"
            >
                <slot name="row" :item="item" />
            </div>
        </div>

        <div v-else class="text-sm text-muted-foreground">
            {{ emptyText }}
        </div>
    </div>
</template>
