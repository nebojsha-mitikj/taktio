<script setup lang="ts">
import CreateTaskButton from '@/components/tasks/CreateTaskButton.vue';
import EmptyTasksMessage from '@/components/tasks/EmptyTasksMessage.vue';
import TaskFormDialog from '@/components/tasks/TaskFormDialog.vue';
import Tasks from '@/components/tasks/Tasks.vue';
import { useTaskDialog } from '@/composables/useTaskDialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { upcoming } from '@/routes/tasks';
import { BreadcrumbItem } from '@/types';
import { LabelWithTasks } from '@/types/labels/Label';
import type { Task } from '@/types/tasks/Task';
import { formatDate } from '@/utils/date';
import { Head } from '@inertiajs/vue3';
import { getLocalTimeZone, today as intlToday } from '@internationalized/date';

const { dialogOpen, editingTask, editTask, createTask } = useTaskDialog();
const tomorrowDate = intlToday(getLocalTimeZone()).add({ days: 1 });

const { tasksByDate, labels } = defineProps<{
    tasksByDate: Record<string, Task[]>;
    labels: LabelWithTasks[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Upcoming', href: upcoming().url },
];
</script>

<template>
    <Head title="Upcoming" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <EmptyTasksMessage
            v-if="Object.keys(tasksByDate).length === 0"
            title="No upcoming tasks"
        />

        <div v-else class="mx-auto my-8 w-full max-w-4xl space-y-8">
            <!-- Page header -->
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Upcoming
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Your tasks for the next few days.
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
                        <span class="text-[11px] text-muted-foreground/50">{{
                            tasks.length
                        }}</span>
                    </div>
                    <Tasks :tasks="tasks" :labels="labels" @edit="editTask" />
                </div>
            </div>
        </div>

        <TaskFormDialog
            v-model:open="dialogOpen"
            :task="editingTask"
            :labels="labels"
            :default-date="tomorrowDate"
        />
        <CreateTaskButton @create="createTask" />
    </AppLayout>
</template>
