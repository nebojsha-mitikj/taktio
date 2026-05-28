import type { TaskPriority } from '@/enums/TaskPriority';
import { Label } from '@/types/labels/Label';

export interface Task {
    id: number;
    user_id: number;
    recurring_task_template_id: number | null;
    title: string;
    description: string | null;
    date: string;
    completed: boolean;
    priority: TaskPriority;
    labels: Label[];
    is_virtual: boolean;
    created_at: string;
    updated_at: string;
}
