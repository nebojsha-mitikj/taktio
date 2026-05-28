<script setup lang="ts">
import { Plus } from 'lucide-vue-next';

const emit = defineEmits<{
    (e: 'create'): void;
}>();
</script>

<template>
    <div class="group fixed right-8 bottom-8 flex items-center">
        <span
            class="pointer-events-none mr-3 rounded-lg border border-black/[0.1] bg-card px-3 py-1.5 text-sm font-medium whitespace-nowrap text-foreground opacity-0 shadow-sm transition-opacity duration-150 group-hover:opacity-100 dark:border-white/[0.1] dark:bg-elevated"
        >
            New task
        </span>
        <button
            @click="emit('create')"
            type="button"
            class="inline-flex h-12 w-12 cursor-pointer items-center justify-center rounded-full border border-black/[0.1] bg-card text-foreground shadow-sm transition-all duration-150 hover:scale-110 hover:bg-black/[0.04] hover:shadow-md active:scale-95 dark:border-white/[0.1] dark:bg-elevated dark:hover:bg-white/[0.06]"
        >
            <Plus class="h-[22px] w-[22px] stroke-[2.5]" />
        </button>
    </div>
</template>
