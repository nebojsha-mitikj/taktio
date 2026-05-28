<script setup lang="ts">
import CreateTaskButton from '@/components/tasks/CreateTaskButton.vue';
import EmptyTasksMessage from '@/components/tasks/EmptyTasksMessage.vue';
import TaskFormDialog from '@/components/tasks/TaskFormDialog.vue';
import Tasks from '@/components/tasks/Tasks.vue';
import { useTaskDialog } from '@/composables/useTaskDialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { history } from '@/routes/tasks';
import { type BreadcrumbItem } from '@/types';
import type { Label } from '@/types/labels/Label';
import type { Task } from '@/types/tasks/Task';
import { formatDate } from '@/utils/date';
import { countCompleted } from '@/utils/taskCounts';
import { Head } from '@inertiajs/vue3';

const { dialogOpen, editingTask, editTask, createTask } = useTaskDialog();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'History', href: history().url },
];

const { tasksByDate, labels } = defineProps<{
    tasksByDate: Record<string, Task[]>;
    labels: Label[];
}>();
</script>

<template>
    <Head title="History" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <EmptyTasksMessage
            v-if="Object.keys(tasksByDate).length === 0"
            title="No completed tasks yet"
            subtitle="Tasks you complete will appear here."
        />

        <div v-else class="mx-auto my-8 w-full max-w-4xl space-y-8">
            <!-- Page header -->
            <div>
                <h1
                    class="text-2xl font-bold tracking-tight text-foreground"
                >
                    History
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Your completed tasks over time.
                </p>
            </div>

            <!-- Date sections -->
            <div class="space-y-6">
                <div
                    v-for="(tasks, date) in tasksByDate"
                    :key="date"
                    class="space-y-2"
                >
                    <div class="flex items-center gap-3">
                        <span
                            class="text-[11px] font-semibold tracking-wide text-muted-foreground"
                        >
                            {{ formatDate(date) }}
                        </span>
                        <div
                            class="h-px flex-1 bg-black/[0.06] dark:bg-white/[0.06]"
                        />
                        <span class="text-[11px] text-muted-foreground/50"
                            >{{ countCompleted(tasks) }}/{{
                                tasks.length
                            }}</span
                        >
                    </div>
                    <Tasks :tasks="tasks" :labels="labels" @edit="editTask" />
                </div>
            </div>
        </div>

        <TaskFormDialog
            :labels="labels"
            v-model:open="dialogOpen"
            :task="editingTask"
        />
        <CreateTaskButton @create="createTask" />
    </AppLayout>
</template>
