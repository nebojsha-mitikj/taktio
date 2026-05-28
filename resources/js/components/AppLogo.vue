<script setup lang="ts">
import { useDarkMode } from '@/composables/useDarkMode';
const { isDark } = useDarkMode();
</script>

<template>
    <img
        :src="isDark ? '/tasks.png' : '/tasks-b.png'"
        class="w-12"
        alt="Todo Tasks application."
    />
    <div class="grid flex-1 text-left text-lg">
        <span
            class="mb-0.5 truncate leading-tight font-semibold text-black dark:text-white"
            style="margin-left: -10px"
        >
            tasks
        </span>
    </div>
</template>
