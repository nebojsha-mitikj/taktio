<script setup lang="ts">
import CreateTaskButton from '@/components/tasks/CreateTaskButton.vue';
import EmptyTasksMessage from '@/components/tasks/EmptyTasksMessage.vue';
import TaskFormDialog from '@/components/tasks/TaskFormDialog.vue';
import Tasks from '@/components/tasks/Tasks.vue';
import { useTaskDialog } from '@/composables/useTaskDialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { today } from '@/routes/tasks';
import type { BreadcrumbItem } from '@/types';
import type { Label } from '@/types/labels/Label';
import { type Task } from '@/types/tasks/Task';
import { countCompleted } from '@/utils/taskCounts';
import { Head } from '@inertiajs/vue3';
import { today as intlToday, getLocalTimeZone } from '@internationalized/date';
import { computed } from 'vue';

const { dialogOpen, editingTask, editTask, createTask } = useTaskDialog();
const todayDate = intlToday(getLocalTimeZone());
const { tasks, labels } = defineProps<{
    tasks: Task[];
    labels: Label[];
}>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Today', href: today().url }];

const completed = computed(() => countCompleted(tasks));
const progress = computed(() =>
    tasks.length > 0 ? (completed.value / tasks.length) * 100 : 0,
);
const allDone = computed(
    () => tasks.length > 0 && completed.value === tasks.length,
);
</script>

<template>
    <Head title="Today" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <EmptyTasksMessage
            v-if="tasks.length === 0"
            title="No tasks for today"
            subtitle="Add a task to get started."
        />

        <div v-else class="mx-auto my-8 w-full max-w-4xl space-y-4">
            <!-- Header -->
            <div class="space-y-3">
                <div class="flex items-baseline justify-between">
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-foreground"
                        >
                            Today
                        </h1>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            Your scheduled tasks for today.
                        </p>
                    </div>
                    <div class="text-right">
                        <span
                            class="text-sm font-semibold"
                            :class="
                                allDone
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : 'text-muted-foreground'
                            "
                        >
                            {{ completed }}/{{ tasks.length }}
                        </span>
                        <p class="text-[11px] text-muted-foreground/60">
                            complete
                        </p>
                    </div>
                </div>

                <!-- Progress bar -->
                <div
                    class="h-1.5 w-full overflow-hidden rounded-full bg-muted"
                >
                    <div
                        class="h-full rounded-full transition-all duration-700 ease-out"
                        :class="allDone ? 'bg-emerald-500' : 'bg-brand'"
                        :style="{ width: Math.max(progress, 2) + '%' }"
                    />
                </div>
            </div>

            <Tasks :tasks="tasks" :labels="labels" @edit="editTask" />
        </div>

        <TaskFormDialog
            :labels="labels"
            v-model:open="dialogOpen"
            :task="editingTask"
            :default-date="todayDate"
        />
        <CreateTaskButton @create="createTask" />
    </AppLayout>
</template>
