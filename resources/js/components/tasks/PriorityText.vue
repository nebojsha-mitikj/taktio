<script setup lang="ts">
import { TaskPriority } from '@/enums/TaskPriority';

const { priority } = defineProps<{
    priority: TaskPriority;
}>();

const badgeClass: Record<TaskPriority, string> = {
    [TaskPriority.HIGH]: 'bg-[#F4823A]/15 text-[#F4823A] border-[#F4823A]/20',
    [TaskPriority.MEDIUM]: 'bg-brand/10 text-brand border-brand/20',
    [TaskPriority.LOW]:
        'bg-black/[0.04] text-muted-foreground border-black/[0.06] dark:bg-white/[0.03] dark:border-white/[0.04]',
    [TaskPriority.NONE]: '',
};

const label: Record<TaskPriority, string> = {
    [TaskPriority.HIGH]: 'High',
    [TaskPriority.MEDIUM]: 'Medium',
    [TaskPriority.LOW]: 'Low',
    [TaskPriority.NONE]: '',
};
</script>

<template>
    <span
        v-if="priority !== TaskPriority.NONE"
        class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-medium"
        :class="badgeClass[priority]"
    >
        {{ label[priority] }}
    </span>
</template>
