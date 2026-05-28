<script setup lang="ts">
import {
    store,
    update,
} from '@/actions/App/Http/Controllers/RecurringTaskTemplateController';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { TaskPriority } from '@/enums/TaskPriority';
import { TaskRecur } from '@/enums/TaskRecur';
import { Weekday } from '@/enums/Weekday';
import { AppPageProps } from '@/types';
import type { Label } from '@/types/labels/Label';
import { CreateRecurringTaskTemplatePayload } from '@/types/recurring-task-templates/CreateRecurringTaskTemplatePayload';
import { RecurringTaskTemplate } from '@/types/recurring-task-templates/RecurringTaskTemplate';
import { capitalizeFirstLetter } from '@/utils/string';
import type { RequestPayload } from '@inertiajs/core';
import { router, usePage } from '@inertiajs/vue3';
import { Repeat, X } from 'lucide-vue-next';
import { AcceptableValue } from 'reka-ui';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const { template, labels } = defineProps<{
    template: RecurringTaskTemplate | null;
    labels: Label[];
}>();

const recurOptions = Object.values(TaskRecur);
const weekdayOptions = Object.values(Weekday);
const recur = ref<TaskRecur | null>(null);
const weekdays = ref<Weekday[]>([]);

const isSubmitting = ref<boolean>(false);
const title = ref<string>('');
const description = ref<string>('');
const priority = ref<TaskPriority | ''>('');
const page = usePage<AppPageProps>();
const selectedLabelIds = ref<number[]>(
    (template?.labels ?? []).map((l) => l.id),
);
const open = defineModel<boolean>('open', { default: false });

const isEditing = computed(() => template !== null);

const submitButtonText = computed(() => {
    if (isSubmitting.value)
        return isEditing.value ? 'Saving...' : 'Creating...';
    return isEditing.value ? 'Save changes' : 'Create recurring task';
});

const resetForm = () => {
    title.value = '';
    description.value = '';
    priority.value = '';
    selectedLabelIds.value = (template?.labels ?? []).map((l) => l.id);
    recur.value = null;
    weekdays.value = [];
};

watch(
    () => template,
    (t: RecurringTaskTemplate | null) => {
        if (!t) {
            resetForm();
            return;
        }
        title.value = t.title;
        description.value = t.description ?? '';
        priority.value = t.priority;
        selectedLabelIds.value = t.labels.map((l) => l.id);
        weekdays.value = t.weekdays ?? [];
        recur.value = t.recur ?? null;
    },
    { immediate: true },
);

const isSubmitDisabled = computed(() => {
    if (isSubmitting.value || !title.value || !priority.value || !recur.value)
        return true;
    if (recur.value === TaskRecur.WEEKLY) return weekdays.value.length === 0;
    return false;
});

const onLabelsChange = (val: AcceptableValue): void => {
    selectedLabelIds.value = val as number[];
};

const recurLabels: Record<TaskRecur, string> = {
    [TaskRecur.DAILY]: 'Every day',
    [TaskRecur.WEEKDAYS]: 'Weekdays (Mon–Fri)',
    [TaskRecur.WEEKENDS]: 'Weekends (Sat–Sun)',
    [TaskRecur.WEEKLY]: 'Weekly (pick days)',
};

const submit = (): void => {
    if (!title.value.trim() || priority.value === '' || recur.value == null)
        return;
    if (isSubmitting.value) return;
    submitRequest({
        title: title.value,
        description: description.value,
        priority: priority.value,
        label_ids: selectedLabelIds.value,
        recur: recur.value,
        weekdays: recur.value === TaskRecur.WEEKLY ? weekdays.value : [],
    });
};

const submitRequest = (
    payload: RequestPayload & CreateRecurringTaskTemplatePayload,
): void => {
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
    if (template === null) {
        router.post(store(), payload, options);
    } else {
        router.put(update(template.id), payload, options);
    }
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="overflow-hidden sm:max-w-lg"
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
                        <Repeat class="size-4 text-muted-foreground" />
                    </span>
                    <div>
                        <DialogTitle
                            class="text-base font-semibold text-foreground"
                        >
                            {{
                                isEditing
                                    ? 'Edit recurring task'
                                    : 'New recurring task'
                            }}
                        </DialogTitle>
                        <DialogDescription
                            class="text-[13px] text-muted-foreground"
                        >
                            {{
                                isEditing
                                    ? 'Update the recurrence settings.'
                                    : 'Set a schedule that repeats automatically.'
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
            <div class="space-y-4 px-6 py-5">
                <!-- Title -->
                <div class="space-y-1.5">
                    <label
                        class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                        >Title</label
                    >
                    <input
                        v-model="title"
                        placeholder="e.g. Morning run, Daily standup…"
                        class="w-full rounded-lg border border-black/[0.1] bg-[#fafafa] px-3 py-2 text-[14px] text-foreground transition-colors outline-none placeholder:text-muted-foreground/50 focus:border-black/30 focus:bg-white dark:border-white/[0.1] dark:bg-white/[0.04] dark:focus:border-white/30 dark:focus:bg-white/[0.06]"
                    />
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <label
                        class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                        >Description
                        <span
                            class="font-normal text-muted-foreground/60 normal-case"
                            >(optional)</span
                        ></label
                    >
                    <textarea
                        v-model="description"
                        placeholder="Add more context..."
                        rows="2"
                        class="w-full resize-none rounded-lg border border-black/[0.1] bg-[#fafafa] px-3 py-2 text-[14px] text-foreground transition-colors outline-none placeholder:text-muted-foreground/50 focus:border-black/30 focus:bg-white dark:border-white/[0.1] dark:bg-white/[0.04] dark:focus:border-white/30 dark:focus:bg-white/[0.06]"
                    />
                </div>

                <!-- Schedule + Priority row -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Recurrence -->
                    <div class="space-y-1.5">
                        <label
                            class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                            >Schedule</label
                        >
                        <Select v-model="recur">
                            <SelectTrigger
                                class="w-full rounded-lg border-black/[0.1] bg-[#fafafa] text-[14px] dark:border-white/[0.1] dark:bg-white/[0.04]"
                            >
                                <SelectValue placeholder="Select…" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem
                                        v-for="option in recurOptions"
                                        :key="option"
                                        :value="option"
                                    >
                                        {{
                                            recurLabels[option] ??
                                            capitalizeFirstLetter(option)
                                        }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Priority -->
                    <div class="space-y-1.5">
                        <label
                            class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                            >Priority</label
                        >
                        <Select v-model="priority">
                            <SelectTrigger
                                class="w-full rounded-lg border-black/[0.1] bg-[#fafafa] text-[14px] dark:border-white/[0.1] dark:bg-white/[0.04]"
                            >
                                <SelectValue placeholder="Select…" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="p in Object.values(TaskPriority)"
                                    :key="p"
                                    :value="p"
                                >
                                    {{ capitalizeFirstLetter(p) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <!-- Weekday picker (only for weekly) -->
                <div v-if="recur === TaskRecur.WEEKLY" class="space-y-1.5">
                    <label
                        class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                        >Days of the week</label
                    >
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="day in weekdayOptions"
                            :key="day"
                            type="button"
                            @click="
                                weekdays = weekdays.includes(day)
                                    ? weekdays.filter((d) => d !== day)
                                    : [...weekdays, day]
                            "
                            class="rounded-lg border px-3 py-1.5 text-[12px] font-semibold transition-colors"
                            :class="
                                weekdays.includes(day)
                                    ? 'border-brand bg-brand text-page'
                                    : 'border-black/[0.1] bg-muted/20 text-muted-foreground hover:bg-black/[0.04] dark:border-white/[0.1] dark:bg-white/[0.04] dark:hover:bg-white/[0.07]'
                            "
                        >
                            {{ capitalizeFirstLetter(day).slice(0, 3) }}
                        </button>
                    </div>
                </div>

                <!-- Labels -->
                <div class="space-y-1.5">
                    <label
                        class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                        >Labels
                        <span
                            class="font-normal text-muted-foreground/60 normal-case"
                            >(optional)</span
                        ></label
                    >
                    <Select
                        multiple
                        :model-value="selectedLabelIds"
                        @update:model-value="onLabelsChange"
                    >
                        <SelectTrigger
                            class="w-full rounded-lg border-black/[0.1] bg-[#fafafa] text-[14px] dark:border-white/[0.1] dark:bg-white/[0.04]"
                        >
                            <SelectValue placeholder="Select labels…" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="label in labels"
                                    :key="label.id"
                                    :value="label.id"
                                >
                                    {{ label.name }}
                                </SelectItem>
                                <p
                                    v-if="!labels.length"
                                    class="px-3 py-2 text-sm text-muted-foreground"
                                >
                                    No labels yet.
                                </p>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
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
                    :disabled="isSubmitDisabled"
                    @click="submit"
                >
                    {{ submitButtonText }}
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
