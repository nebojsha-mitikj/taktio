import type { Task } from '@/types/tasks/Task';
import { ref } from 'vue';

export function useTaskDialog() {
    const dialogOpen = ref(false);
    const editingTask = ref<Task | null>(null);

    const editTask = (task: Task): void => {
        editingTask.value = task;
        dialogOpen.value = true;
    };

    const createTask = (): void => {
        editingTask.value = null;
        dialogOpen.value = true;
    };

    return {
        dialogOpen,
        editingTask,
        editTask,
        createTask,
    };
}
