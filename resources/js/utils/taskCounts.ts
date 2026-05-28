import type { Task } from '@/types/tasks/Task';

export const countCompleted = (tasks: Task[]): number => {
    return tasks.filter((t) => t.completed).length;
};
