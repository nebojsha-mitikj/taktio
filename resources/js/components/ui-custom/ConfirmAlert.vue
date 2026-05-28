<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { AlertTriangle } from 'lucide-vue-next';
import { computed } from 'vue';

const emit = defineEmits<{
    (e: 'submit'): void;
    (e: 'update:open', value: boolean): void;
}>();

const props = withDefaults(
    defineProps<{
        open: boolean;
        title?: string;
        description?: string;
        requestIsActive: boolean;
        confirmLabel?: string;
    }>(),
    {
        title: 'Are you sure?',
        description: 'This action cannot be undone.',
        confirmLabel: 'Confirm',
    },
);

const openProxy = computed({
    get: () => props.open,
    set: (value: boolean) => emit('update:open', value),
});

const submit = (): void => {
    emit('submit');
    emit('update:open', false);
};
</script>

<template>
    <AlertDialog v-model:open="openProxy">
        <AlertDialogContent>
            <!-- Header -->
            <div
                class="border-b border-black/[0.06] px-6 py-5 dark:border-white/[0.06]"
            >
                <div class="flex items-center gap-3">
                    <span
                        class="flex size-8 items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10"
                    >
                        <AlertTriangle class="size-4 text-red-500" />
                    </span>
                    <div>
                        <AlertDialogTitle
                            class="text-base font-semibold text-foreground"
                        >
                            {{ props.title }}
                        </AlertDialogTitle>
                        <AlertDialogDescription
                            class="text-[13px] text-muted-foreground"
                        >
                            {{ props.description }}
                        </AlertDialogDescription>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-2 px-6 py-4">
                <button
                    type="button"
                    class="cursor-pointer rounded-lg px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-black/[0.05] disabled:opacity-50 dark:hover:bg-white/[0.05]"
                    :disabled="props.requestIsActive"
                    @click="openProxy = false"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="cursor-pointer rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700 disabled:opacity-40"
                    :disabled="props.requestIsActive"
                    @click="submit"
                >
                    {{
                        props.requestIsActive
                            ? 'Deleting...'
                            : props.confirmLabel
                    }}
                </button>
            </div>
        </AlertDialogContent>
    </AlertDialog>
</template>
