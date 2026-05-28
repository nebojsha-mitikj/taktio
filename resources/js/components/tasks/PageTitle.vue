<script setup lang="ts">
const props = defineProps<{
    title: string;
    subtitle?: string;
}>();
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">
            {{ props.title }}
        </h1>
        <p v-if="props.subtitle" class="mt-1 text-sm text-muted-foreground">
            {{ props.subtitle }}
        </p>
    </div>
</template>
