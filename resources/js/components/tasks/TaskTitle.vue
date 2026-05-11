<script setup lang="ts">
import { TaskStatus } from '@/enums/TaskStatus';
import StatusBadge from '@/components/ui-custom/StatusBadge.vue';
import type { Task } from '@/types/tasks/Task';

const props = defineProps<{
    task: Task;
}>();
</script>

<template>
    <div class="flex items-center gap-2">
        <p
            class="text-[15px] font-medium leading-snug"
            :class="props.task.status === TaskStatus.COMPLETED
                ? 'text-[#bbb] line-through dark:text-[#555]'
                : 'text-[#111] dark:text-white'"
        >
            {{ props.task.title }}
        </p>
        <StatusBadge v-if="props.task.recurring_task_template_id !== null || props.task.is_virtual">
            Recurring
        </StatusBadge>
    </div>
</template>
