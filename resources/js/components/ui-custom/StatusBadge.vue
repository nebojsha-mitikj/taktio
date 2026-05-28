<script setup lang="ts">
defineProps<{
    variant?: 'green' | 'gray';
}>();
</script>

<template>
    <span
        class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-medium"
        :class="
            variant === 'gray'
                ? 'border-black/[0.04] bg-black/[0.03] text-muted-foreground/60 dark:border-white/[0.04] dark:bg-white/[0.03]'
                : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-800/40 dark:bg-emerald-950/40 dark:text-emerald-400'
        "
    >
        <slot />
    </span>
</template>
