<script setup lang="ts">
import { store, update } from '@/actions/App/Http/Controllers/TaskController';
import { Calendar } from '@/components/ui/calendar';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { TaskPriority } from '@/enums/TaskPriority';
import { cn } from '@/lib/utils';
import { AppPageProps } from '@/types';
import type { Label } from '@/types/labels/Label';
import type { CreateTaskPayload } from '@/types/tasks/CreateTaskPayload';
import type { Task } from '@/types/tasks/Task';
import { capitalizeFirstLetter } from '@/utils/string';
import type { RequestPayload } from '@inertiajs/core';
import { router, usePage } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import {
    DateFormatter,
    getLocalTimeZone,
    parseDate,
    today,
} from '@internationalized/date';
import { CalendarIcon, SquareCheck, X } from 'lucide-vue-next';
import { AcceptableValue } from 'reka-ui';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const { task, labels, defaultDate } = defineProps<{
    task: Task | null;
    labels: Label[];
    defaultDate?: DateValue;
}>();

const date = ref<DateValue | undefined>();
const datePopover = ref<boolean>(false);
const df = new DateFormatter('en-US', { dateStyle: 'long' });

const isSubmitting = ref<boolean>(false);
const title = ref<string>('');
const description = ref<string>('');
const priority = ref<TaskPriority | ''>(TaskPriority.NONE);
const page = usePage<AppPageProps>();
const selectedLabelIds = ref<number[]>((task?.labels ?? []).map((l) => l.id));
const open = defineModel<boolean>('open', { default: false });

const isEditing = computed(() => task !== null);

const submitButtonText = computed(() => {
    if (isSubmitting.value)
        return isEditing.value ? 'Saving...' : 'Creating...';
    return isEditing.value ? 'Save changes' : 'Create task';
});

const resetForm = () => {
    title.value = '';
    description.value = '';
    date.value = defaultDate;
    priority.value = TaskPriority.NONE;
    selectedLabelIds.value = (task?.labels ?? []).map((l) => l.id);
};

watch(
    () => task,
    (t: Task | null) => {
        if (!t) {
            resetForm();
            return;
        }
        title.value = t.title;
        description.value = t.description ?? '';
        priority.value = t.priority;
        const dateOnly = t.date.includes('T') ? t.date.split('T')[0] : t.date;
        date.value = parseDate(dateOnly);
        selectedLabelIds.value = t.labels.map((l) => l.id);
    },
    { immediate: true },
);

const isSubmitDisabled = computed(
    () => isSubmitting.value || !title.value || !priority.value || !date.value,
);

const onLabelsChange = (val: AcceptableValue): void => {
    selectedLabelIds.value = val as number[];
};

const submit = (): void => {
    if (!title.value.trim() || date.value == null || priority.value === '') {
        return;
    }
    if (isSubmitting.value) return;
    submitRequest({
        title: title.value,
        description: description.value,
        date: date.value.toString(),
        priority: priority.value,
        label_ids: selectedLabelIds.value,
    });
};

const submitRequest = (payload: RequestPayload & CreateTaskPayload): void => {
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
    if (task === null) {
        router.post(store(), payload, options);
    } else if (task.is_virtual) {
        router.post(
            store(),
            {
                ...payload,
                recurring_task_template_id: task.recurring_task_template_id,
            },
            options,
        );
    } else {
        router.put(update(task.id), payload, options);
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
                        <SquareCheck class="size-4 text-muted-foreground" />
                    </span>
                    <div>
                        <DialogTitle
                            class="text-base font-semibold text-foreground"
                        >
                            {{ isEditing ? 'Edit task' : 'New task' }}
                        </DialogTitle>
                        <DialogDescription
                            class="text-[13px] text-muted-foreground"
                        >
                            {{
                                isEditing
                                    ? 'Update the details below.'
                                    : 'Fill in the details to create a task.'
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
                        placeholder="What needs to be done?"
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

                <!-- Date + Priority row -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Date -->
                    <div class="space-y-1.5">
                        <label
                            class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                            >Date</label
                        >
                        <Popover v-model:open="datePopover">
                            <PopoverTrigger as-child>
                                <button
                                    :class="
                                        cn(
                                            'flex w-full items-center gap-2 rounded-lg border border-black/[0.1] bg-[#fafafa] px-3 py-2 text-[14px] transition-colors outline-none hover:bg-white dark:border-white/[0.1] dark:bg-white/[0.04] dark:hover:bg-white/[0.06]',
                                            date
                                                ? 'text-foreground'
                                                : 'text-muted-foreground/50',
                                        )
                                    "
                                >
                                    <CalendarIcon
                                        class="size-3.5 flex-shrink-0"
                                    />
                                    <span class="truncate text-left">{{
                                        date
                                            ? df.format(
                                                  date.toDate(
                                                      getLocalTimeZone(),
                                                  ),
                                              )
                                            : 'Pick a date'
                                    }}</span>
                                </button>
                            </PopoverTrigger>
                            <PopoverContent class="w-auto p-0" align="start">
                                <Calendar
                                    v-model="date"
                                    :initial-focus="true"
                                    layout="month-and-year"
                                    :minValue="today(getLocalTimeZone())"
                                    @update:model-value="
                                        (v) => {
                                            if (v) {
                                                date = v;
                                                datePopover = false;
                                            }
                                        }
                                    "
                                />
                            </PopoverContent>
                        </Popover>
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
