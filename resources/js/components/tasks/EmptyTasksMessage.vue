<script setup lang="ts">
const { title, subtitle = "You're all caught up for now." } = defineProps<{
    title: string;
    subtitle?: string;
}>();
</script>

<template>
    <div class="mx-auto my-8 w-full max-w-4xl">
        <div
            class="rounded-xl border border-black/[0.07] bg-card px-6 py-12 text-center shadow-sm dark:border-white/[0.07]"
        >
            <p class="text-[15px] font-semibold text-foreground">
                {{ title }}
            </p>
            <p class="mt-1 text-sm text-muted-foreground">
                {{ subtitle }}
            </p>
        </div>
    </div>
</template>
