<script setup lang="ts">
import RecurringTaskTemplates from '@/components/recurring-task-templates/RecurringTaskTemplates.vue';
import RecurringTemplateFormDialog from '@/components/recurring-task-templates/RecurringTemplateFormDialog.vue';
import CreateTaskButton from '@/components/tasks/CreateTaskButton.vue';
import TaskFormDialog from '@/components/tasks/TaskFormDialog.vue';
import { useRecurringTemplateDialog } from '@/composables/useRecurringTemplateDialog';
import { useTaskDialog } from '@/composables/useTaskDialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/recurring';
import { type BreadcrumbItem } from '@/types';
import type { Label } from '@/types/labels/Label';
import type { RecurringTaskTemplate } from '@/types/recurring-task-templates/RecurringTaskTemplate';
import { Head } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

const { dialogOpen, editingTemplate, editTemplate, createTemplate } =
    useRecurringTemplateDialog();

const { dialogOpen: taskDialogOpen, editingTask, createTask } = useTaskDialog();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Recurring', href: index().url },
];

const { labels, templates } = defineProps<{
    labels: Label[];
    templates: RecurringTaskTemplate[];
}>();
</script>

<template>
    <Head title="Recurring" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-8 w-full max-w-4xl space-y-4">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-foreground"
                    >
                        Recurring tasks
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Tasks that repeat on a schedule so you never forget.
                    </p>
                </div>
                <button
                    @click="
                        ($event.currentTarget as HTMLElement).blur();
                        createTemplate();
                    "
                    class="flex w-full cursor-pointer items-center justify-center gap-1.5 rounded-lg border border-black/[0.1] bg-card px-3 py-1.5 text-sm font-medium text-foreground shadow-sm transition-colors hover:bg-black/[0.04] sm:w-auto sm:justify-start dark:border-white/[0.1] dark:bg-elevated dark:hover:bg-white/[0.06]"
                >
                    <Plus class="size-3.5" />
                    Add recurring
                </button>
            </div>

            <div
                v-if="templates.length === 0"
                class="rounded-xl border border-black/[0.07] bg-card px-6 py-12 text-center shadow-sm dark:border-white/[0.07]"
            >
                <p class="text-[15px] font-semibold text-foreground">
                    No recurring tasks yet
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    Add one to keep your habits on track.
                </p>
            </div>
            <RecurringTaskTemplates
                v-else
                :templates="templates"
                :labels="labels"
                @edit="editTemplate"
            />
        </div>

        <RecurringTemplateFormDialog
            v-model:open="dialogOpen"
            :template="editingTemplate"
            :labels="labels"
        />
        <TaskFormDialog
            :labels="labels"
            v-model:open="taskDialogOpen"
            :task="editingTask"
        />
        <CreateTaskButton @create="createTask" />
    </AppLayout>
</template>
