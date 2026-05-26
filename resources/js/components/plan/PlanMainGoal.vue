<script setup lang="ts">
import { update as updatePlan } from '@/actions/App/Http/Controllers/PlanController';
import { router } from '@inertiajs/vue3';
import { Check } from 'lucide-vue-next';
import { ref, type ComponentPublicInstance } from 'vue';

const { mainGoal, isPast, year, month } = defineProps<{
    mainGoal: string | null;
    isPast: boolean;
    year: number;
    month: number;
}>();

const paddedMonth = (m: number): string => String(m).padStart(2, '0');

const editingMainGoal = ref(false);
const mainGoalDraft = ref(mainGoal ?? '');

const focusAtEnd = (el: Element | ComponentPublicInstance | null): void => {
    if (!(el instanceof HTMLInputElement)) return;
    if (document.activeElement === el) return;
    el.focus();
    el.setSelectionRange(el.value.length, el.value.length);
};

const saveMainGoal = (): void => {
    if (mainGoalDraft.value === (mainGoal ?? '')) {
        editingMainGoal.value = false;
        return;
    }
    router.put(updatePlan({ year, month: paddedMonth(month) }).url, { main_goal: mainGoalDraft.value }, {
        preserveScroll: true,
        onFinish: () => { editingMainGoal.value = false; },
    });
};
</script>

<template>
    <div class="space-y-1.5">
        <p class="text-xs font-semibold uppercase tracking-wider text-[#999] dark:text-[#666]">Main goal</p>

        <div
            class="rounded-xl bg-white shadow-sm ring-1 ring-inset ring-black/[0.07] dark:bg-[#111] dark:ring-white/[0.07]"
            :class="!isPast && !editingMainGoal ? 'cursor-pointer' : ''"
            @click="!isPast && !editingMainGoal && (editingMainGoal = true, mainGoalDraft = mainGoal ?? '')"
        >
            <div class="flex items-center gap-2 px-5 py-4">
                <input
                    v-if="editingMainGoal"
                    v-model="mainGoalDraft"
                    class="min-w-0 flex-1 bg-transparent p-0 text-[15px] leading-snug text-[#111] focus:outline-none dark:text-white"
                    @blur="saveMainGoal"
                    @keydown.enter="saveMainGoal"
                    @keydown.escape="editingMainGoal = false"
                    @click.stop
                    :ref="focusAtEnd"
                />
                <p
                    v-else
                    class="flex-1 text-[15px] leading-snug"
                    :class="mainGoal ? 'text-[#111] dark:text-white' : 'text-[#aaa] dark:text-[#555]'"
                >
                    {{ mainGoal || (isPast ? 'No main goal was set.' : 'Click to set a main goal…') }}
                </p>
                <button
                    class="shrink-0 cursor-pointer rounded p-1 text-[#888] transition-colors hover:text-[#111] dark:hover:text-white"
                    :class="editingMainGoal ? '' : 'invisible pointer-events-none'"
                    @mousedown.prevent
                    @click.stop="saveMainGoal"
                >
                    <Check class="size-3.5" />
                </button>
            </div>
        </div>
    </div>
</template>
