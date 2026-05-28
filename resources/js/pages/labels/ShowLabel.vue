<script setup lang="ts">
import CreateTaskButton from '@/components/tasks/CreateTaskButton.vue';
import TaskFormDialog from '@/components/tasks/TaskFormDialog.vue';
import Tasks from '@/components/tasks/Tasks.vue';
import { useTaskDialog } from '@/composables/useTaskDialog';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { LabelWithTasks } from '@/types/labels/Label';
import type { Task } from '@/types/tasks/Task';
import { formatDate, toDate } from '@/utils/date';
import { Head } from '@inertiajs/vue3';
import { Tag } from 'lucide-vue-next';
import { computed } from 'vue';

const { dialogOpen, editingTask, editTask, createTask } = useTaskDialog();

const { label, labels } = defineProps<{
    label: LabelWithTasks;
    labels: LabelWithTasks[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Labels', href: '/labels' },
    { title: label.name, href: `/labels/${label.id}` },
];

const tasksByDate = computed<Record<string, Task[]>>(() => {
    return label.tasks.reduce<Record<string, Task[]>>((acc, task) => {
        const key = toDate(task.date);
        (acc[key] ??= []).push(task);
        return acc;
    }, {});
});

const colorClass = 'text-muted-foreground bg-black/[0.05] dark:bg-white/[0.06]';
</script>

<template>
    <Head :title="label.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-8 w-full max-w-4xl space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <span
                    class="flex size-11 flex-shrink-0 items-center justify-center rounded-xl"
                    :class="colorClass"
                >
                    <Tag class="size-5" />
                </span>
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-foreground"
                    >
                        {{ label.name }}
                    </h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        {{
                            label.tasks.length > 0
                                ? label.tasks.length +
                                  ' task' +
                                  (label.tasks.length !== 1 ? 's' : '')
                                : 'No tasks for this label.'
                        }}
                    </p>
                </div>
            </div>

            <!-- Empty state -->
            <div
                v-if="label.tasks.length === 0"
                class="rounded-xl border border-black/[0.07] bg-card px-6 py-12 text-center shadow-sm dark:border-white/[0.07]"
            >
                <p class="text-sm text-muted-foreground">
                    No tasks for this label yet.
                </p>
            </div>

            <!-- Date sections -->
            <div v-else class="space-y-6">
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
