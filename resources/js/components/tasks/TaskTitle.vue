<script setup lang="ts">
import StatusBadge from '@/components/ui-custom/StatusBadge.vue';
import type { Task } from '@/types/tasks/Task';

const props = defineProps<{
    task: Task;
}>();
</script>

<template>
    <div class="flex items-center gap-2">
        <p
            class="text-[15px] leading-snug font-medium"
            :class="
                props.task.completed
                    ? 'text-muted-foreground/50 line-through'
                    : 'text-foreground'
            "
        >
            {{ props.task.title }}
        </p>
        <StatusBadge
            v-if="
                props.task.recurring_task_template_id !== null ||
                props.task.is_virtual
            "
        >
            Recurring
        </StatusBadge>
    </div>
</template>
