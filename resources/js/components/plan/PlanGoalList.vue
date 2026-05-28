<script setup lang="ts">
import {
    store as storeGoal,
    update as updateGoal,
} from '@/actions/App/Http/Controllers/PlanGoalController';
import {
    store as storeStep,
    update as updateStep,
} from '@/actions/App/Http/Controllers/PlanGoalStepController';
import PlanGoalActions from '@/components/plan/PlanGoalActions.vue';
import PlanStepActions from '@/components/plan/PlanStepActions.vue';
import { Checkbox } from '@/components/ui/checkbox';
import type { PlanGoal, PlanGoalStep } from '@/types/plan/Plan';
import { router } from '@inertiajs/vue3';
import { Check, Plus } from 'lucide-vue-next';
import { computed, nextTick, ref, type ComponentPublicInstance } from 'vue';

const { goals, isPast, year, month } = defineProps<{
    goals: PlanGoal[];
    isPast: boolean;
    year: number;
    month: number;
}>();

const paddedMonth = (m: number): string => String(m).padStart(2, '0');

const focusAtEnd = (el: Element | ComponentPublicInstance | null): void => {
    if (!(el instanceof HTMLInputElement)) return;
    if (document.activeElement === el) return;
    el.focus();
    el.setSelectionRange(el.value.length, el.value.length);
};

const sortedGoals = computed(() => [...goals].sort((a, b) => b.id - a.id));

const sortedSteps = (goal: PlanGoal): PlanGoalStep[] => [...goal.steps];

// Add goal
const addingGoal = ref(false);
const addingGoalLoading = ref(false);
const newGoalTitle = ref('');
const newGoalInputRef = ref<HTMLInputElement | null>(null);

const openAddGoal = (): void => {
    addingGoal.value = true;
    nextTick(() => newGoalInputRef.value?.focus());
};

const cancelAddGoal = (): void => {
    if (addingGoalLoading.value) return;
    addingGoal.value = false;
    newGoalTitle.value = '';
};

const onNewGoalBlur = (): void => {
    if (addingGoalLoading.value) return;
    if (newGoalTitle.value.trim()) {
        submitNewGoal();
    } else {
        addingGoal.value = false;
        newGoalTitle.value = '';
    }
};

const submitNewGoal = (): void => {
    const title = newGoalTitle.value.trim();
    if (!title || addingGoalLoading.value) return;
    addingGoalLoading.value = true;
    router.post(
        storeGoal({ year, month: paddedMonth(month) }).url,
        { title },
        {
            preserveScroll: true,
            onSuccess: () => {
                newGoalTitle.value = '';
                addingGoal.value = false;
            },
            onFinish: () => {
                addingGoalLoading.value = false;
            },
        },
    );
};

// Goal editing
const editingGoalId = ref<number | null>(null);
const goalDraft = ref('');

const startEditGoal = (goal: PlanGoal): void => {
    editingGoalId.value = goal.id;
    goalDraft.value = goal.title;
};

const saveGoal = (goal: PlanGoal): void => {
    if (goalDraft.value.trim() === goal.title || !goalDraft.value.trim()) {
        editingGoalId.value = null;
        return;
    }
    router.put(
        updateGoal(goal.id).url,
        { title: goalDraft.value.trim() },
        {
            preserveScroll: true,
            onFinish: () => {
                editingGoalId.value = null;
            },
        },
    );
};

const toggleGoal = (goal: PlanGoal): void => {
    router.put(
        updateGoal(goal.id).url,
        { completed: !goal.completed },
        { preserveScroll: true },
    );
};

// Add a step
const newStepGoalId = ref<number | null>(null);
const newStepTitle = ref('');
const addingStepLoading = ref(false);

const openAddStep = (goal: PlanGoal): void => {
    newStepGoalId.value = goal.id;
    newStepTitle.value = '';
};

const cancelAddStep = (): void => {
    if (addingStepLoading.value) return;
    newStepGoalId.value = null;
    newStepTitle.value = '';
};

const onNewStepBlur = (goal: PlanGoal): void => {
    if (addingStepLoading.value) return;
    if (newStepTitle.value.trim()) {
        submitNewStep(goal);
    } else {
        newStepGoalId.value = null;
    }
};

const submitNewStep = (goal: PlanGoal): void => {
    const title = newStepTitle.value.trim();
    if (!title || addingStepLoading.value) return;
    addingStepLoading.value = true;
    router.post(
        storeStep(goal.id).url,
        { title },
        {
            preserveScroll: true,
            onSuccess: () => {
                newStepTitle.value = '';
                newStepGoalId.value = null;
            },
            onFinish: () => {
                addingStepLoading.value = false;
            },
        },
    );
};

// Step editing
const editingStepId = ref<number | null>(null);
const stepDraft = ref('');

const startEditStep = (step: PlanGoalStep): void => {
    editingStepId.value = step.id;
    stepDraft.value = step.title;
};

const saveStep = (goal: PlanGoal, step: PlanGoalStep): void => {
    if (stepDraft.value.trim() === step.title || !stepDraft.value.trim()) {
        editingStepId.value = null;
        return;
    }
    router.put(
        updateStep({ goal: goal.id, step: step.id }).url,
        { title: stepDraft.value.trim() },
        {
            preserveScroll: true,
            onFinish: () => {
                editingStepId.value = null;
            },
        },
    );
};

const toggleStep = (goal: PlanGoal, step: PlanGoalStep): void => {
    router.put(
        updateStep({ goal: goal.id, step: step.id }).url,
        { completed: !step.completed },
        { preserveScroll: true },
    );
};
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <p
                class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
            >
                Goals
            </p>
            <button
                v-if="!isPast"
                class="flex cursor-pointer items-center gap-1 text-xs text-muted-foreground/50 transition-colors hover:text-muted-foreground"
                @click="openAddGoal"
            >
                <Plus class="size-3" />
                Add goal
            </button>
        </div>

        <!-- Goals list -->
        <div
            v-if="goals.length > 0 || addingGoal"
            class="rounded-xl bg-card shadow-sm ring-1 ring-black/[0.07] ring-inset dark:ring-white/[0.07]"
        >
            <!-- Wrapper keeps last:border-b-0 isolated from the add-goal sibling -->
            <div>
                <div
                    v-for="goal in sortedGoals"
                    :key="goal.id"
                    class="border-b border-black/[0.05] last:border-b-0 dark:border-white/[0.05]"
                >
                    <!-- Goal row -->
                    <div
                        class="flex items-center gap-3 px-5 py-4"
                        :class="
                            !isPast && editingGoalId !== goal.id
                                ? 'cursor-pointer'
                                : ''
                        "
                        @click="
                            !isPast &&
                            editingGoalId !== goal.id &&
                            startEditGoal(goal)
                        "
                    >
                        <div class="flex shrink-0 items-center" @click.stop>
                            <Checkbox
                                :model-value="goal.completed"
                                :disabled="isPast"
                                @update:model-value="toggleGoal(goal)"
                            />
                        </div>

                        <div class="flex min-w-0 flex-1 items-center">
                            <input
                                v-if="editingGoalId === goal.id"
                                v-model="goalDraft"
                                class="w-full bg-transparent p-0 text-[15px] leading-snug font-medium text-foreground focus:outline-none"
                                @blur="saveGoal(goal)"
                                @keydown.enter="saveGoal(goal)"
                                @keydown.escape="editingGoalId = null"
                                @click.stop
                                :ref="focusAtEnd"
                            />
                            <span
                                v-else
                                class="block text-[15px] leading-snug font-medium transition-colors"
                                :class="
                                    goal.completed
                                        ? 'text-muted-foreground/50 line-through'
                                        : 'text-foreground'
                                "
                            >
                                {{ goal.title }}
                            </span>
                        </div>

                        <div v-if="!isPast" class="flex shrink-0 items-center">
                            <button
                                v-if="editingGoalId === goal.id"
                                class="flex size-7 cursor-pointer items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                                @mousedown.prevent
                                @click.stop="saveGoal(goal)"
                            >
                                <Check class="size-3.5" />
                            </button>
                            <PlanGoalActions
                                v-else
                                :goal="goal"
                                @edit="startEditGoal(goal)"
                                @add-step="openAddStep(goal)"
                            />
                        </div>
                    </div>

                    <!-- Steps -->
                    <div
                        v-if="
                            goal.steps.length > 0 || newStepGoalId === goal.id
                        "
                        class="border-t border-black/[0.05] px-5 pt-1 pb-2 dark:border-white/[0.05]"
                    >
                        <div
                            v-for="step in sortedSteps(goal)"
                            :key="step.id"
                            class="flex items-center gap-3 py-2"
                            :class="
                                !isPast && editingStepId !== step.id
                                    ? 'cursor-pointer'
                                    : ''
                            "
                            @click="
                                !isPast &&
                                editingStepId !== step.id &&
                                startEditStep(step)
                            "
                        >
                            <div class="w-4 shrink-0" />
                            <div class="flex shrink-0 items-center" @click.stop>
                                <Checkbox
                                    :model-value="step.completed"
                                    :disabled="isPast"
                                    @update:model-value="toggleStep(goal, step)"
                                />
                            </div>

                            <div class="flex min-w-0 flex-1 items-center">
                                <input
                                    v-if="editingStepId === step.id"
                                    v-model="stepDraft"
                                    class="w-full bg-transparent p-0 text-[15px] leading-snug font-medium text-foreground focus:outline-none"
                                    @blur="saveStep(goal, step)"
                                    @keydown.enter="saveStep(goal, step)"
                                    @keydown.escape="editingStepId = null"
                                    @click.stop
                                    :ref="focusAtEnd"
                                />
                                <span
                                    v-else
                                    class="block text-[15px] leading-snug font-medium transition-colors"
                                    :class="
                                        step.completed
                                            ? 'text-muted-foreground/50 line-through'
                                            : 'text-foreground'
                                    "
                                >
                                    {{ step.title }}
                                </span>
                            </div>

                            <button
                                v-if="!isPast && editingStepId === step.id"
                                class="flex size-7 shrink-0 cursor-pointer items-center justify-center rounded-full text-muted-foreground transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                                @mousedown.prevent
                                @click.stop="saveStep(goal, step)"
                            >
                                <Check class="size-3.5" />
                            </button>
                            <PlanStepActions
                                v-else-if="!isPast"
                                :goal="goal"
                                :step="step"
                                @edit="startEditStep(step)"
                            />
                        </div>

                        <!-- Add step input -->
                        <div
                            v-if="newStepGoalId === goal.id"
                            class="flex items-center gap-3 py-1.5"
                        >
                            <div class="w-4 shrink-0" />
                            <div class="size-4 shrink-0" />
                            <div class="flex flex-1 items-center gap-2">
                                <input
                                    v-model="newStepTitle"
                                    placeholder="Step title…"
                                    class="flex-1 bg-transparent p-0 text-[15px] leading-snug font-medium text-foreground placeholder-muted-foreground/40 focus:outline-none disabled:opacity-50"
                                    :disabled="addingStepLoading"
                                    @keydown.enter="submitNewStep(goal)"
                                    @keydown.escape="cancelAddStep"
                                    @blur="onNewStepBlur(goal)"
                                    :ref="
                                        (el) =>
                                            el &&
                                            nextTick(() =>
                                                (
                                                    el as HTMLInputElement
                                                ).focus(),
                                            )
                                    "
                                />
                                <button
                                    class="cursor-pointer rounded p-0.5 text-muted-foreground hover:text-foreground disabled:opacity-40"
                                    :disabled="addingStepLoading"
                                    @mousedown.prevent
                                    @click.stop="submitNewStep(goal)"
                                >
                                    <Check class="size-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add goal input -->
            <div
                v-if="addingGoal"
                class="flex items-center gap-3 px-5 py-3.5"
                :class="
                    goals.length > 0
                        ? 'border-t border-black/[0.05] dark:border-white/[0.05]'
                        : ''
                "
            >
                <div class="size-4 shrink-0" />
                <input
                    v-model="newGoalTitle"
                    placeholder="New goal title…"
                    class="flex-1 bg-transparent p-0 text-[15px] font-medium text-foreground placeholder-muted-foreground/40 focus:outline-none disabled:opacity-50"
                    :disabled="addingGoalLoading"
                    ref="newGoalInputRef"
                    @keydown.enter="submitNewGoal"
                    @keydown.escape="cancelAddGoal"
                    @blur="onNewGoalBlur"
                />
                <button
                    v-if="newGoalTitle.trim()"
                    class="cursor-pointer rounded p-0.5 text-muted-foreground hover:text-foreground disabled:opacity-40"
                    :disabled="addingGoalLoading"
                    @mousedown.prevent
                    @click.stop="submitNewGoal"
                >
                    <Check class="size-3.5" />
                </button>
            </div>
        </div>

        <!-- Empty state -->
        <div
            v-else-if="goals.length === 0"
            class="rounded-xl bg-card px-6 py-8 text-center shadow-sm ring-1 ring-black/[0.07] ring-inset dark:ring-white/[0.07]"
            :class="!isPast ? 'cursor-pointer' : ''"
            @click="!isPast && openAddGoal()"
        >
            <p class="text-sm text-muted-foreground">
                {{
                    isPast
                        ? 'No goals were set for this month.'
                        : 'No goals yet.'
                }}
            </p>
        </div>
    </div>
</template>
