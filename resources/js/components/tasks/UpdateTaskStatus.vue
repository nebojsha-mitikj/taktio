<script setup lang="ts">
import {
    materialize,
    updateCompleted,
} from '@/actions/App/Http/Controllers/TaskController';
import { Checkbox } from '@/components/ui/checkbox';
import type { Task } from '@/types/tasks/Task';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{ task: Task }>();
const isUpdating = ref(false);

const toggle = (): void => {
    if (isUpdating.value) return;
    const completed = !props.task.completed;
    isUpdating.value = true;

    const options = {
        preserveScroll: true,
        onSuccess: () => toast.success('Task updated successfully.'),
        onFinish: () => {
            isUpdating.value = false;
        },
    };

    if (props.task.is_virtual && props.task.recurring_task_template_id) {
        router.post(
            materialize(props.task.recurring_task_template_id),
            { date: props.task.date, completed },
            options,
        );
    } else {
        router.put(updateCompleted(props.task.id), { completed }, options);
    }
};
</script>

<template>
    <Checkbox
        :model-value="task.completed"
        @update:model-value="toggle"
    />
</template>
