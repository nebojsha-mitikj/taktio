<script setup lang="ts">
import { store, update } from '@/actions/App/Http/Controllers/LabelController';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import type { AppPageProps } from '@/types';
import type { CreateLabelPayload } from '@/types/labels/CreateLabelPayload';
import type { Label } from '@/types/labels/Label';
import type { RequestPayload } from '@inertiajs/core';
import { router, usePage } from '@inertiajs/vue3';
import { Tag, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const isSubmitting = ref<boolean>(false);
const name = ref<string>('');

const page = usePage<AppPageProps>();

const { label } = defineProps<{
    label: Label | null;
}>();

const open = defineModel<boolean>('open', { default: false });

const isEditing = computed(() => label !== null);

const submitButtonText = computed(() => {
    if (isSubmitting.value)
        return isEditing.value ? 'Saving...' : 'Creating...';
    return isEditing.value ? 'Save changes' : 'Create label';
});

const resetForm = () => {
    name.value = '';
};

watch(
    () => label,
    (t: Label | null) => {
        if (!t) {
            resetForm();
            return;
        }
        name.value = t.name;
    },
    { immediate: true },
);

const submit = (): void => {
    if (name.value.trim().length === 0) return;
    submitRequest({ name: name.value });
};

const submitRequest = (payload: RequestPayload & CreateLabelPayload): void => {
    isSubmitting.value = true;
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
            toast.success(page.props.flash?.success ?? '');
            resetForm();
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    };
    if (label === null) {
        router.post(store(), payload, options);
    } else {
        router.put(update(label.id), payload, options);
    }
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="overflow-hidden sm:max-w-sm"
            @open-auto-focus="(e) => e.preventDefault()"
        >
            <!-- Header -->
            <div
                class="flex items-start justify-between border-b border-black/[0.06] px-6 py-5 dark:border-white/[0.06]"
            >
                <div class="flex items-center gap-3">
                    <span
                        class="flex size-8 items-center justify-center rounded-lg bg-black/[0.05] dark:bg-white/[0.06]"
                    >
                        <Tag class="size-4 text-muted-foreground" />
                    </span>
                    <div>
                        <DialogTitle
                            class="text-base font-semibold text-foreground"
                        >
                            {{ isEditing ? 'Edit label' : 'New label' }}
                        </DialogTitle>
                        <DialogDescription
                            class="text-[13px] text-muted-foreground"
                        >
                            {{
                                isEditing
                                    ? 'Rename this label.'
                                    : 'Organize tasks with a label.'
                            }}
                        </DialogDescription>
                    </div>
                </div>
                <button
                    class="flex size-7 cursor-pointer items-center justify-center rounded-lg text-muted-foreground/60 transition-colors hover:bg-black/[0.06] hover:text-foreground dark:hover:bg-white/[0.06]"
                    @click="open = false"
                >
                    <X class="size-4" />
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
                <div class="space-y-1.5">
                    <label
                        class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                        >Name</label
                    >
                    <input
                        v-model="name"
                        placeholder="e.g. Work, Personal, Health…"
                        class="w-full rounded-lg border border-black/[0.1] bg-[#fafafa] px-3 py-2 text-[14px] text-foreground transition-colors outline-none placeholder:text-muted-foreground/50 focus:border-black/30 focus:bg-white dark:border-white/[0.1] dark:bg-white/[0.04] dark:focus:border-white/30 dark:focus:bg-white/[0.06]"
                        @keydown.enter="submit"
                    />
                </div>
            </div>

            <!-- Footer -->
            <div
                class="flex items-center justify-end gap-2 border-t border-black/[0.06] px-6 py-4 dark:border-white/[0.06]"
            >
                <button
                    type="button"
                    class="cursor-pointer rounded-lg px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-black/[0.05] disabled:opacity-50 dark:hover:bg-white/[0.05]"
                    :disabled="isSubmitting"
                    @click="open = false"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="cursor-pointer rounded-lg bg-brand px-4 py-2 text-sm font-medium text-page transition-colors hover:bg-brand-hover disabled:opacity-40"
                    :disabled="isSubmitting || !name"
                    @click="submit"
                >
                    {{ submitButtonText }}
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
