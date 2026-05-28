<script setup lang="ts">
import TooltipButton from '@/components/ui-custom/TooltipButton.vue';
import { Pencil } from 'lucide-vue-next';

const { tooltip } = defineProps<{
    tooltip?: string;
}>();
const emit = defineEmits<{ (e: 'edit'): void }>();
</script>

<template>
    <TooltipButton
        :tooltip="tooltip ?? 'Edit'"
        :icon="Pencil"
        @click="emit('edit')"
    />
</template>
