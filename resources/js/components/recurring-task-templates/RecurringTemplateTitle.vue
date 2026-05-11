<script setup lang="ts">
import StatusBadge from '@/components/ui-custom/StatusBadge.vue';
import type { RecurringTaskTemplate } from '@/types/recurring-task-templates/RecurringTaskTemplate';

const { template } = defineProps<{
    template: RecurringTaskTemplate;
}>();
</script>

<template>
    <div class="flex items-center gap-2">
        <p class="text-[15px] font-medium text-[#111] dark:text-white">{{ template.title }}</p>
        <StatusBadge :variant="template.is_active ? 'green' : 'gray'">
            {{ template.is_active ? 'Active' : 'Inactive' }}
        </StatusBadge>
    </div>
</template>
