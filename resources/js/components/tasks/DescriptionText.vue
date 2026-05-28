<script setup lang="ts">
const { text } = defineProps<{ text?: string | null }>();
</script>

<template>
    <p
        v-if="text != null && text !== ''"
        class="text-xs whitespace-pre-wrap text-muted-foreground"
    >
        {{ text }}
    </p>
</template>
