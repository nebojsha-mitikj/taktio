<script setup lang="ts">
import { show as showPlan } from '@/actions/App/Http/Controllers/PlanController';
import PlanGoalList from '@/components/plan/PlanGoalList.vue';
import PlanMainGoal from '@/components/plan/PlanMainGoal.vue';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Plan } from '@/types/plan/Plan';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { plan, year, month, mode } = defineProps<{
    plan: Plan | null;
    year: number;
    month: number;
    mode: 'past' | 'current' | 'future';
}>();

const isPast = computed(
    () => mode === 'past',
);

const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
];

const pageTitle = computed(
    () => `${monthNames[month - 1]} ${year}`,
);

const paddedMonth = (m: number): string => String(m).padStart(2, '0');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Plan', href: '/plan' },
    { title: pageTitle.value, href: showPlan({ year, month: paddedMonth(month) }).url },
];

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 11 }, (_, i) => currentYear - 5 + i);
const selectedYear = ref(String(year));
const selectedMonth = ref(String(month));

const navigate = (): void => {
    router.visit(showPlan({ year: selectedYear.value, month: paddedMonth(Number(selectedMonth.value)) }).url);
};
</script>

<template>
    <Head :title="pageTitle" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto my-8 w-full max-w-4xl space-y-6 px-4 sm:px-0">

            <!-- Header -->
            <div class="space-y-1">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-foreground">
                            {{ pageTitle }}
                        </h1>
                        <p class="mt-0.5 text-sm text-muted-foreground">Your goals for the month.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <Select v-model="selectedMonth" @update:model-value="navigate">
                            <SelectTrigger class="w-32">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="(name, i) in monthNames" :key="i + 1" :value="String(i + 1)">
                                    {{ name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Select v-model="selectedYear" @update:model-value="navigate">
                            <SelectTrigger class="w-24">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="y in years" :key="y" :value="String(y)">
                                    {{ y }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <p v-if="isPast" class="text-sm text-muted-foreground/60">
                    This month is in the past — view only.
                </p>
            </div>

            <!-- No plan for past month -->
            <div
                v-if="!plan"
                class="rounded-xl bg-card px-6 py-12 text-center shadow-sm ring-1 ring-inset ring-black/[0.07] dark:ring-white/[0.07]"
            >
                <p class="text-[15px] font-semibold text-foreground">No plan for this month</p>
                <p class="mt-1 text-sm text-muted-foreground">Nothing was planned here.</p>
            </div>

            <template v-else>
                <PlanMainGoal
                    :main-goal="plan.main_goal"
                    :is-past="isPast"
                    :year="year"
                    :month="month"
                />
                <PlanGoalList
                    :goals="plan.goals"
                    :is-past="isPast"
                    :year="year"
                    :month="month"
                />
            </template>

        </div>
    </AppLayout>
</template>
