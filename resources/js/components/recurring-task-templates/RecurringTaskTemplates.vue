<script setup lang="ts">
import LabelBadgeList from '@/components/labels/LabelBadgeList.vue';
import TaskListCard from '@/components/list/TaskListCard.vue';
import RecurringTemplateActions from '@/components/recurring-task-templates/RecurringTemplateActions.vue';
import RecurringTemplatePattern from '@/components/recurring-task-templates/RecurringTemplatePattern.vue';
import RecurringTemplateTitle from '@/components/recurring-task-templates/RecurringTemplateTitle.vue';
import DescriptionText from '@/components/tasks/DescriptionText.vue';
import PriorityText from '@/components/tasks/PriorityText.vue';
import type { Label } from '@/types/labels/Label';
import type { RecurringTaskTemplate } from '@/types/recurring-task-templates/RecurringTaskTemplate';

const { templates, labels } = defineProps<{
    templates: RecurringTaskTemplate[];
    labels: Label[];
}>();

const emit = defineEmits<{
    (e: 'edit', template: RecurringTaskTemplate): void;
}>();
</script>

<template>
    <TaskListCard :items="templates" empty-text="No recurring tasks yet.">
        <template #row="{ item: template }">
            <div class="min-w-0 flex-1 space-y-1.5">
                <RecurringTemplateTitle :template="template" />

                <DescriptionText :text="template.description" />

                <div class="flex flex-wrap items-center gap-1.5">
                    <PriorityText :priority="template.priority" />
                    <RecurringTemplatePattern :template="template" />
                    <LabelBadgeList :labels="template.labels" />
                </div>
            </div>

            <RecurringTemplateActions
                :template="template"
                :labels="labels"
                @edit="emit('edit', $event)"
            />
        </template>
    </TaskListCard>
</template>
