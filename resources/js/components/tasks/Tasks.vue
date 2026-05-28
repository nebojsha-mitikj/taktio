<script setup lang="ts">
import LabelBadgeList from '@/components/labels/LabelBadgeList.vue';
import TaskListCard from '@/components/list/TaskListCard.vue';
import DescriptionText from '@/components/tasks/DescriptionText.vue';
import PriorityText from '@/components/tasks/PriorityText.vue';
import TaskActions from '@/components/tasks/TaskActions.vue';
import TaskStatus from '@/components/tasks/TaskStatus.vue';
import TaskTitle from '@/components/tasks/TaskTitle.vue';
import { usePageMatch } from '@/composables/usePageMatch';
import { TaskPriority } from '@/enums/TaskPriority';
import { TaskStatus as TaskStatusEnum } from '@/enums/TaskStatus';
import type { Label } from '@/types/labels/Label';
import type { Task } from '@/types/tasks/Task';

const { tasks, labels } = defineProps<{
    tasks: Task[];
    labels: Label[];
}>();

const emit = defineEmits<{
    (e: 'edit', task: Task): void;
}>();

const { isMatch: isTodayMatch } = usePageMatch('tasks/Today');
const { isMatch: isHistoryMatch } = usePageMatch('tasks/History');

const priorityDot: Record<TaskPriority, string> = {
    [TaskPriority.HIGH]:   'bg-[#F4823A]',
    [TaskPriority.MEDIUM]: 'bg-brand',
    [TaskPriority.LOW]:    'bg-muted-foreground/40',
    [TaskPriority.NONE]:   'bg-transparent',
};
</script>

<template>
    <TaskListCard :items="tasks" empty-text="No tasks.">
        <template #row="{ item: task }">
            <!-- Priority dot indicator -->
            <div
                class="mt-1.5 size-1.5 flex-shrink-0 rounded-full"
                :class="task.status === TaskStatusEnum.COMPLETED
                    ? 'bg-black/[0.1] dark:bg-white/[0.1]'
                    : priorityDot[task.priority]"
            />

            <div class="flex-1 space-y-1.5 min-w-0">
                <TaskTitle :task="task" />

                <DescriptionText :text="task.description" />

                <div class="flex flex-wrap items-center gap-1.5">
                    <template v-if="!isHistoryMatch">
                        <TaskStatus
                            v-if="isTodayMatch && task.status === TaskStatusEnum.IN_PROGRESS"
                            :task="task"
                        />
                        <PriorityText
                            v-if="task.status !== TaskStatusEnum.COMPLETED"
                            :priority="task.priority"
                        />
                    </template>
                    <LabelBadgeList :labels="task.labels" />
                </div>
            </div>

            <TaskActions
                v-if="!isHistoryMatch"
                :task="task"
                :labels="labels"
                @edit="emit('edit', $event)"
            />
        </template>
    </TaskListCard>
</template>
