<script setup lang="ts">
import { home, login, register } from '@/routes';
import { today } from '@/routes/tasks';
import { AppPageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { canRegister } = defineProps<{
    canRegister: boolean;
}>();

const page = usePage<AppPageProps>();

const isAuthenticated = computed(() => page.props.auth.user !== null);
</script>

<template>
    <Head />

    <div class="flex min-h-screen flex-col bg-page text-foreground">
        <!-- ── Header ── -->
        <header
            class="sticky top-0 z-10 border-b border-white/[0.06] bg-page/80 backdrop-blur-md"
        >
            <div
                class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4 sm:px-8"
            >
                <Link
                    :href="home()"
                    class="text-xl font-bold text-foreground transition-all hover:scale-[1.02] hover:opacity-70"
                >
                    Taktio
                </Link>

                <div class="flex items-center gap-2">
                    <Link
                        v-if="isAuthenticated"
                        :href="today()"
                        class="rounded-lg bg-brand px-4 py-1.5 text-sm font-medium text-page transition-all hover:bg-brand-hover"
                    >
                        Today
                    </Link>

                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-lg px-4 py-1.5 text-sm font-medium text-muted-foreground transition-all hover:text-foreground"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="rounded-lg bg-brand px-4 py-1.5 text-sm font-medium text-page transition-all hover:bg-brand-hover"
                        >
                            Get started
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex flex-col">
            <!-- ── Hero ── -->
            <section
                class="border-b border-white/[0.05] px-6 py-24 text-center sm:px-8 sm:py-32"
            >
                <p
                    class="mb-5 text-xs font-medium tracking-[0.24em] text-brand uppercase"
                >
                    Personal task manager
                </p>
                <h1
                    class="mb-6 text-5xl leading-tight font-bold tracking-tight text-foreground sm:text-7xl"
                >
                    Plan today.<br />Keep your rhythm.
                </h1>
                <p
                    class="mx-auto mb-10 max-w-lg text-base leading-relaxed text-muted-foreground"
                >
                    Taktio helps you manage daily tasks, recurring routines,
                    labels, and monthly goals — all in one focused place.
                </p>
                <div
                    class="flex flex-col items-center gap-3 sm:flex-row sm:justify-center"
                >
                    <Link
                        v-if="isAuthenticated"
                        :href="today()"
                        class="rounded-xl bg-brand px-8 py-3.5 text-sm font-semibold text-page transition-all hover:bg-brand-hover active:scale-[0.99]"
                    >
                        Go to Today
                    </Link>
                    <template v-else>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="rounded-xl bg-brand px-8 py-3.5 text-sm font-semibold text-page transition-all hover:bg-brand-hover active:scale-[0.99]"
                        >
                            Get started
                        </Link>
                        <Link
                            :href="login()"
                            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                        >
                            Log in →
                        </Link>
                    </template>
                </div>
            </section>

            <!-- ── [1] Task Organization ── -->
            <section class="bg-surface py-28">
                <div class="mx-auto max-w-6xl px-6 sm:px-8">
                    <div
                        class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2"
                    >
                        <!-- Text -->
                        <div>
                            <p
                                class="mb-4 text-xs font-semibold tracking-[0.2em] text-brand uppercase"
                            >
                                Task Organization
                            </p>
                            <h2
                                class="mb-5 text-4xl leading-tight font-bold tracking-tight text-foreground sm:text-5xl"
                            >
                                Everything for today. And beyond.
                            </h2>
                            <p
                                class="mb-8 text-base leading-relaxed text-muted-foreground"
                            >
                                See what needs your attention now, plan what's
                                coming next, and look back at what you've
                                already completed.
                            </p>
                            <ul class="space-y-3">
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Today view</strong
                                        >
                                        — focus only on what matters right
                                        now</span
                                    >
                                </li>
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Upcoming view</strong
                                        >
                                        — see what's planned for the next
                                        days</span
                                    >
                                </li>
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >History</strong
                                        >
                                        — review completed tasks</span
                                    >
                                </li>
                            </ul>
                        </div>

                        <!-- Mockup: task list -->
                        <div
                            class="rounded-2xl border border-white/[0.07] bg-[#1a1b21] p-1 shadow-2xl shadow-black/60"
                        >
                            <div
                                class="flex gap-1 border-b border-white/[0.06] px-3 pt-3 pb-0"
                            >
                                <button
                                    class="rounded-t-lg bg-brand/15 px-4 py-2 text-xs font-semibold text-brand"
                                >
                                    Today
                                </button>
                                <button
                                    class="px-4 py-2 text-xs font-medium text-muted-foreground"
                                >
                                    Upcoming
                                </button>
                                <button
                                    class="px-4 py-2 text-xs font-medium text-muted-foreground"
                                >
                                    History
                                </button>
                            </div>

                            <div class="p-4">
                                <p
                                    class="mb-3 text-xs font-medium text-muted-foreground"
                                >
                                    Today · May 26
                                </p>

                                <div class="space-y-2">
                                    <div
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-white/[0.03]"
                                    >
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span
                                            class="flex-1 text-sm text-foreground"
                                            >Buy groceries</span
                                        >
                                        <span
                                            class="rounded-md bg-[#F4823A]/15 px-2 py-0.5 text-[10px] font-medium text-[#F4823A]"
                                            >high</span
                                        >
                                        <span
                                            class="rounded-md bg-[#F4823A]/10 px-2 py-0.5 text-[10px] font-medium text-[#F4823A]"
                                            >shopping</span
                                        >
                                    </div>

                                    <div
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-white/[0.03]"
                                    >
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span
                                            class="flex-1 text-sm text-foreground"
                                            >Finish project report</span
                                        >
                                        <span
                                            class="rounded-md bg-brand/10 px-2 py-0.5 text-[10px] font-medium text-brand"
                                            >med</span
                                        >
                                        <span
                                            class="rounded-md bg-brand/10 px-2 py-0.5 text-[10px] font-medium text-brand"
                                            >work</span
                                        >
                                    </div>

                                    <div
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-white/[0.03]"
                                    >
                                        <div
                                            class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-brand"
                                        >
                                            <svg
                                                class="h-2.5 w-2.5 text-page"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="3"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                        </div>
                                        <span
                                            class="flex-1 text-sm text-muted-foreground line-through"
                                            >Morning run</span
                                        >
                                        <span
                                            class="rounded-md bg-brand/10 px-2 py-0.5 text-[10px] font-medium text-brand"
                                            >health</span
                                        >
                                    </div>

                                    <div
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-white/[0.03]"
                                    >
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span
                                            class="flex-1 text-sm text-foreground"
                                            >Call dentist</span
                                        >
                                        <span
                                            class="rounded-md bg-[#F4823A]/15 px-2 py-0.5 text-[10px] font-medium text-[#F4823A]"
                                            >high</span
                                        >
                                    </div>
                                </div>

                                <div
                                    class="my-4 border-t border-white/[0.06]"
                                ></div>

                                <p
                                    class="mb-3 text-xs font-medium text-muted-foreground"
                                >
                                    Upcoming · Tomorrow
                                </p>
                                <div class="space-y-2">
                                    <div
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-white/[0.03]"
                                    >
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span
                                            class="flex-1 text-sm text-foreground"
                                            >Team standup</span
                                        >
                                        <span
                                            class="rounded-md bg-[#F4823A]/15 px-2 py-0.5 text-[10px] font-medium text-[#F4823A]"
                                            >high</span
                                        >
                                        <span
                                            class="rounded-md bg-brand/10 px-2 py-0.5 text-[10px] font-medium text-brand"
                                            >work</span
                                        >
                                    </div>

                                    <div
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-white/[0.03]"
                                    >
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span
                                            class="flex-1 text-sm text-foreground"
                                            >Read 30 minutes</span
                                        >
                                        <span
                                            class="rounded-md bg-[#8b5cf6]/10 px-2 py-0.5 text-[10px] font-medium text-[#8b5cf6]"
                                            >reading</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── [3] Planner ── -->
            <section class="bg-page py-36">
                <div class="mx-auto max-w-6xl px-6 sm:px-8">
                    <div
                        class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2"
                    >
                        <!-- Mockup: planner -->
                        <div
                            class="order-last rounded-2xl border border-white/[0.07] bg-[#1a1b21] p-5 shadow-2xl shadow-black/60 lg:order-first"
                        >
                            <div class="mb-5 flex items-center justify-between">
                                <span
                                    class="text-sm font-semibold text-foreground"
                                    >May 2026</span
                                >
                                <span
                                    class="rounded-lg bg-brand/15 px-3 py-1 text-xs font-medium text-brand"
                                    >Monthly Plan</span
                                >
                            </div>

                            <!-- Goal 1: AWS cert -->
                            <div class="mb-5">
                                <div class="mb-2.5 flex items-center gap-2">
                                    <span
                                        class="h-2 w-2 shrink-0 rounded-sm bg-brand"
                                    ></span>
                                    <span
                                        class="text-sm font-semibold text-foreground"
                                        >AWS Solutions Architect cert</span
                                    >
                                </div>
                                <div
                                    class="ml-5 space-y-2 border-l border-white/[0.08] pl-4"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#F4823A]"
                                        >
                                            <svg
                                                class="h-2.5 w-2.5 text-page"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="3"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-xs text-muted-foreground line-through"
                                            >IAM &amp; EC2 modules</span
                                        >
                                        <span
                                            class="ml-auto rounded-md bg-[#F4823A]/10 px-2 py-0.5 text-[10px] font-medium text-[#F4823A]"
                                            >done</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#F4823A]"
                                        >
                                            <svg
                                                class="h-2.5 w-2.5 text-page"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="3"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-xs text-muted-foreground line-through"
                                            >S3 &amp; networking review</span
                                        >
                                        <span
                                            class="ml-auto rounded-md bg-[#F4823A]/10 px-2 py-0.5 text-[10px] font-medium text-[#F4823A]"
                                            >done</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span class="text-xs text-foreground"
                                            >Complete practice exam</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span class="text-xs text-foreground"
                                            >Book the real exam</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Goal 2: Run 5k -->
                            <div class="mb-5">
                                <div class="mb-2.5 flex items-center gap-2">
                                    <span
                                        class="h-2 w-2 shrink-0 rounded-sm bg-[#F4823A]"
                                    ></span>
                                    <span
                                        class="text-sm font-semibold text-foreground"
                                        >Run 5k consistently</span
                                    >
                                </div>
                                <div
                                    class="ml-5 space-y-2 border-l border-white/[0.08] pl-4"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#F4823A]"
                                        >
                                            <svg
                                                class="h-2.5 w-2.5 text-page"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="3"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-xs text-muted-foreground line-through"
                                            >Start 8-week running plan</span
                                        >
                                        <span
                                            class="ml-auto rounded-md bg-[#F4823A]/10 px-2 py-0.5 text-[10px] font-medium text-[#F4823A]"
                                            >done</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span class="text-xs text-foreground"
                                            >Complete week 4 sessions</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span class="text-xs text-foreground"
                                            >First 5k under 30 min</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Goal 3: Reading -->
                            <div>
                                <div class="mb-2.5 flex items-center gap-2">
                                    <span
                                        class="h-2 w-2 shrink-0 rounded-sm bg-[#8b5cf6]"
                                    ></span>
                                    <span
                                        class="text-sm font-semibold text-foreground"
                                        >Read 12 books this year</span
                                    >
                                </div>
                                <div
                                    class="ml-5 space-y-2 border-l border-white/[0.08] pl-4"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#F4823A]"
                                        >
                                            <svg
                                                class="h-2.5 w-2.5 text-page"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="3"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-xs text-muted-foreground line-through"
                                            >Atomic Habits</span
                                        >
                                        <span
                                            class="ml-auto rounded-md bg-[#F4823A]/10 px-2 py-0.5 text-[10px] font-medium text-[#F4823A]"
                                            >done</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span class="text-xs text-foreground"
                                            >Deep Work</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                        ></div>
                                        <span class="text-xs text-foreground"
                                            >The Pragmatic Programmer</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Text -->
                        <div class="order-first lg:order-last">
                            <p
                                class="mb-4 text-xs font-semibold tracking-[0.2em] text-brand uppercase"
                            >
                                Monthly Planner
                            </p>
                            <h2
                                class="mb-5 text-4xl leading-tight font-bold tracking-tight text-foreground sm:text-6xl"
                            >
                                Big goals.<br />Broken down.
                            </h2>
                            <p
                                class="mb-8 text-base leading-relaxed text-muted-foreground"
                            >
                                Set monthly goals, break them into steps, and
                                track progress without losing sight of the
                                bigger picture.
                            </p>
                            <ul class="space-y-3">
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Monthly goals</strong
                                        >
                                        — choose what you're working
                                        toward</span
                                    >
                                </li>
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Concrete steps</strong
                                        >
                                        — turn goals into actions</span
                                    >
                                </li>
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Progress at a glance</strong
                                        >
                                        — see what's done and what's left</span
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── [4] Recurring Tasks ── -->
            <section class="bg-surface py-20">
                <div class="mx-auto max-w-6xl px-6 sm:px-8">
                    <div
                        class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2"
                    >
                        <!-- Text -->
                        <div>
                            <p
                                class="mb-4 text-xs font-semibold tracking-[0.2em] text-brand uppercase"
                            >
                                Recurring Tasks
                            </p>
                            <h2
                                class="mb-5 text-4xl leading-tight font-bold tracking-tight text-foreground sm:text-5xl"
                            >
                                Set once,<br />show up daily.
                            </h2>
                            <p
                                class="mb-8 text-base leading-relaxed text-muted-foreground"
                            >
                                Create tasks that repeat automatically — daily,
                                weekly, on weekdays, weekends, or only on the
                                days you choose.
                            </p>
                            <ul class="space-y-3">
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Daily</strong
                                        >
                                        — repeat every single day</span
                                    >
                                </li>
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Weekdays / Weekends</strong
                                        >
                                        — match your work or rest routine</span
                                    >
                                </li>
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Custom weekly days</strong
                                        >
                                        — pick exactly when tasks appear</span
                                    >
                                </li>
                            </ul>
                        </div>

                        <!-- Mockup: recurring schedules -->
                        <div
                            class="rounded-2xl border border-white/[0.07] bg-[#1a1b21] p-5 shadow-2xl shadow-black/60"
                        >
                            <p
                                class="mb-4 text-sm font-semibold text-foreground"
                            >
                                Recurring Schedules
                            </p>

                            <div class="space-y-4">
                                <div
                                    class="rounded-xl border border-white/[0.06] bg-white/[0.03] p-4"
                                >
                                    <div
                                        class="mb-3 flex items-center justify-between"
                                    >
                                        <span
                                            class="text-sm font-medium text-foreground"
                                            >Morning standup</span
                                        >
                                        <span
                                            class="rounded-md bg-brand/15 px-2 py-0.5 text-[10px] font-semibold text-brand"
                                            >Daily</span
                                        >
                                    </div>
                                    <div class="flex gap-1.5">
                                        <span
                                            v-for="d in [
                                                'M',
                                                'T',
                                                'W',
                                                'T',
                                                'F',
                                                'S',
                                                'S',
                                            ]"
                                            :key="d + 'daily'"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-brand/30 bg-brand/20 text-[10px] font-semibold text-brand"
                                            >{{ d }}</span
                                        >
                                    </div>
                                </div>

                                <div
                                    class="rounded-xl border border-white/[0.06] bg-white/[0.03] p-4"
                                >
                                    <div
                                        class="mb-3 flex items-center justify-between"
                                    >
                                        <span
                                            class="text-sm font-medium text-foreground"
                                            >Evening workout</span
                                        >
                                        <span
                                            class="rounded-md bg-[#F4823A]/15 px-2 py-0.5 text-[10px] font-semibold text-[#F4823A]"
                                            >Weekends</span
                                        >
                                    </div>
                                    <div class="flex gap-1.5">
                                        <span
                                            v-for="(day, i) in [
                                                { l: 'M', a: false },
                                                { l: 'T', a: false },
                                                { l: 'W', a: false },
                                                { l: 'T', a: false },
                                                { l: 'F', a: false },
                                                { l: 'S', a: true },
                                                { l: 'S', a: true },
                                            ]"
                                            :key="i + 'wknd'"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg text-[10px] font-semibold"
                                            :class="
                                                day.a
                                                    ? 'border border-[#F4823A]/30 bg-[#F4823A]/20 text-[#F4823A]'
                                                    : 'bg-white/[0.05] text-muted-foreground'
                                            "
                                            >{{ day.l }}</span
                                        >
                                    </div>
                                </div>

                                <div
                                    class="rounded-xl border border-white/[0.06] bg-white/[0.03] p-4"
                                >
                                    <div
                                        class="mb-3 flex items-center justify-between"
                                    >
                                        <span
                                            class="text-sm font-medium text-foreground"
                                            >Weekly review</span
                                        >
                                        <span
                                            class="rounded-md bg-brand/15 px-2 py-0.5 text-[10px] font-semibold text-brand"
                                            >Weekly</span
                                        >
                                    </div>
                                    <div class="flex gap-1.5">
                                        <span
                                            v-for="(day, i) in [
                                                { l: 'M', a: false },
                                                { l: 'T', a: false },
                                                { l: 'W', a: false },
                                                { l: 'T', a: false },
                                                { l: 'F', a: true },
                                                { l: 'S', a: false },
                                                { l: 'S', a: false },
                                            ]"
                                            :key="i + 'wkly'"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg text-[10px] font-semibold"
                                            :class="
                                                day.a
                                                    ? 'border border-brand/30 bg-brand/20 text-brand'
                                                    : 'bg-white/[0.05] text-muted-foreground'
                                            "
                                            >{{ day.l }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── [5] Labels ── -->
            <section class="bg-page py-32">
                <div class="mx-auto max-w-6xl px-6 sm:px-8">
                    <div
                        class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2"
                    >
                        <!-- Mockup: labels -->
                        <div
                            class="order-last rounded-2xl border border-white/[0.07] bg-[#1a1b21] p-5 shadow-2xl shadow-black/60 lg:order-first"
                        >
                            <p
                                class="mb-4 text-sm font-semibold text-foreground"
                            >
                                Your Labels
                            </p>

                            <div class="mb-5 flex flex-wrap gap-2">
                                <span
                                    v-for="(label, i) in [
                                        'work',
                                        'gym',
                                        'study',
                                        'reading',
                                        'aws',
                                        'health',
                                    ]"
                                    :key="label"
                                    class="flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium"
                                    :class="
                                        i % 3 === 0
                                            ? 'border border-brand/30 bg-brand/10 text-brand'
                                            : i % 3 === 1
                                              ? 'border border-[#F4823A]/30 bg-[#F4823A]/10 text-[#F4823A]'
                                              : 'border border-[#8b5cf6]/30 bg-[#8b5cf6]/10 text-[#8b5cf6]'
                                    "
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full"
                                        :class="
                                            i % 3 === 0
                                                ? 'bg-brand'
                                                : i % 3 === 1
                                                  ? 'bg-[#F4823A]'
                                                  : 'bg-[#8b5cf6]'
                                        "
                                    ></span>
                                    {{ label }}
                                </span>
                            </div>

                            <div
                                class="mb-4 flex items-center gap-2 border-t border-white/[0.06] pt-4"
                            >
                                <span class="text-xs text-muted-foreground"
                                    >Filtered by:</span
                                >
                                <span
                                    class="flex items-center gap-1.5 rounded-full border border-brand/30 bg-brand/10 px-3 py-1 text-xs font-medium text-brand"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-brand"
                                    ></span>
                                    work
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div
                                    v-for="task in [
                                        'Review pull requests',
                                        'Team standup',
                                        'Write deployment docs',
                                    ]"
                                    :key="task"
                                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/[0.03]"
                                >
                                    <div
                                        class="h-4 w-4 shrink-0 rounded-full border-2 border-white/20"
                                    ></div>
                                    <span
                                        class="flex-1 text-sm text-foreground"
                                        >{{ task }}</span
                                    >
                                    <span
                                        class="flex items-center gap-1 rounded-full border border-brand/30 bg-brand/10 px-2 py-0.5 text-[10px] font-medium text-brand"
                                        >work</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Text -->
                        <div class="order-first lg:order-last">
                            <p
                                class="mb-4 text-xs font-semibold tracking-[0.2em] text-brand uppercase"
                            >
                                Labels
                            </p>
                            <h2
                                class="mb-5 text-4xl leading-tight font-bold tracking-tight text-foreground sm:text-5xl"
                            >
                                Group by context.
                            </h2>
                            <p
                                class="mb-8 text-base leading-relaxed text-muted-foreground"
                            >
                                Organize tasks with custom labels, then filter
                                your day by work, health, study, reading, or
                                anything else that matters.
                            </p>
                            <ul class="space-y-3">
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Custom labels</strong
                                        >
                                        — create as many as you need</span
                                    >
                                </li>
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Apply to any task</strong
                                        >
                                        — one-offs and recurring tasks</span
                                    >
                                </li>
                                <li
                                    class="flex items-start gap-3 text-sm text-muted-foreground"
                                >
                                    <span class="mt-0.5 text-brand">✦</span>
                                    <span
                                        ><strong class="text-foreground"
                                            >Filter by label</strong
                                        >
                                        — focus on the right context</span
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── [6] Final CTA ── -->
            <section class="bg-surface py-32 text-center">
                <div class="mx-auto max-w-2xl px-6 sm:px-8">
                    <h2
                        class="mb-5 text-4xl leading-tight font-bold tracking-tight text-foreground sm:text-5xl"
                    >
                        Your tasks.<br />Your rhythm.
                    </h2>
                    <p
                        class="mx-auto mb-10 max-w-lg text-base leading-relaxed text-muted-foreground"
                    >
                        Free to use and open-source. Start with today, build
                        your rhythm, and shape the app your way.
                    </p>

                    <div
                        class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center"
                    >
                        <Link
                            v-if="isAuthenticated"
                            :href="today()"
                            class="rounded-xl bg-brand px-8 py-3.5 text-sm font-semibold text-page transition-all hover:bg-brand-hover active:scale-[0.99]"
                        >
                            Go to Today
                        </Link>

                        <template v-else>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="rounded-xl bg-brand px-8 py-3.5 text-sm font-semibold text-page transition-all hover:bg-brand-hover active:scale-[0.99]"
                            >
                                Get started
                            </Link>

                            <Link
                                :href="login()"
                                class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                            >
                                Log in →
                            </Link>
                        </template>
                    </div>
                </div>
            </section>
        </main>

        <!-- ── Footer ── -->
        <footer class="bg-surface px-6 py-6 sm:px-8">
            <div class="flex flex-col items-center gap-3">
                <a
                    href="https://github.com/nebojsha-mitikj/taktio"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-muted-foreground transition-all hover:scale-125 hover:text-foreground"
                >
                    <svg
                        class="h-7 w-7"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"
                        />
                    </svg>
                </a>
                <span class="text-xs text-muted-foreground/60"
                    >© 2026 Taktio</span
                >
            </div>
        </footer>
    </div>
</template>
