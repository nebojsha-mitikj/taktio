<script setup lang="ts">
import { index as planIndex } from '@/actions/App/Http/Controllers/PlanController';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { toUrl, urlIsActive } from '@/lib/utils';
import { show as getLabel, index as getLabels } from '@/routes/labels';
import { index as recurringTemplates } from '@/routes/recurring';
import { history, today, upcoming } from '@/routes/tasks';
import type { AppPageProps, NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Calendar,
    ChevronDown,
    Circle,
    Clock,
    History,
    LayoutList,
    Menu,
    Repeat,
    Star,
    Tag,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Label = { id: number; name: string };

const page = usePage<AppPageProps<{ labels: Label[] }>>();
const auth = computed(() => page.props.auth);
const labels = computed(() => page.props.labels ?? []);
const mobileOpen = ref(false);
const labelsOpen = ref(false);
const moreOpen = ref(false);

const isMoreActive = computed(
    () => page.url.startsWith('/upcoming') || page.url.startsWith('/history'),
);

const isCurrentRoute = computed(
    () => (item: NavItem) => urlIsActive(item.href, page.url),
);

const navItems: NavItem[] = [{ title: 'Today', href: today(), icon: Star }];

// Touch flags — suppress the synthetic mouseenter+click that fire after touchstart
let touchHandledMore = false;
let touchHandledLabels = false;

function onMoreMouseEnter() {
    if (touchHandledMore) return;
    moreOpen.value = true;
}
function onMoreChevronTouch() {
    touchHandledMore = true;
    moreOpen.value = !moreOpen.value;
}
function onMoreChevronClick() {
    if (touchHandledMore) {
        touchHandledMore = false;
        return;
    }
    moreOpen.value = !moreOpen.value;
}

function onLabelsMouseEnter() {
    if (touchHandledLabels) return;
    labelsOpen.value = true;
}
function onLabelsChevronTouch() {
    touchHandledLabels = true;
    labelsOpen.value = !labelsOpen.value;
}
function onLabelsChevronClick() {
    if (touchHandledLabels) {
        touchHandledLabels = false;
        return;
    }
    labelsOpen.value = !labelsOpen.value;
}
</script>

<template>
    <header
        class="sticky top-0 z-10 border-b border-black/[0.06] bg-background/80 backdrop-blur-md dark:border-white/[0.06] dark:bg-page/80"
    >
        <div
            class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4 sm:px-8"
        >
            <!-- Logo -->
            <Link
                :href="today()"
                class="text-xl font-black transition-all hover:scale-[1.02] hover:opacity-70"
                >Taktio</Link
            >

            <!-- Desktop Nav -->
            <nav class="hidden items-center gap-1 md:flex">
                <Link
                    v-for="item in navItems"
                    :key="item.title"
                    :href="item.href"
                    class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        isCurrentRoute(item)
                            ? 'bg-black/[0.06] text-foreground dark:bg-white/[0.08] dark:text-foreground'
                            : 'text-muted-foreground'
                    "
                >
                    <component :is="item.icon" class="size-3.5" />
                    {{ item.title }}
                </Link>

                <!-- More hover dropdown (Upcoming + History) -->
                <div
                    class="relative"
                    @mouseenter="onMoreMouseEnter"
                    @mouseleave="moreOpen = false"
                >
                    <div
                        class="flex items-center rounded-md transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                        :class="
                            isMoreActive
                                ? 'bg-black/[0.06] text-foreground dark:bg-white/[0.08] dark:text-foreground'
                                : 'text-muted-foreground'
                        "
                    >
                        <Link
                            :href="upcoming()"
                            class="flex items-center gap-1.5 rounded-l-md py-1.5 pr-2 pl-3 text-sm font-medium outline-none"
                        >
                            <Clock class="size-3.5" />
                            Timeline
                        </Link>
                        <button
                            type="button"
                            class="flex cursor-pointer items-center rounded-r-md py-1.5 pr-2 pl-0.5 outline-none"
                            @touchstart.passive="onMoreChevronTouch"
                            @click.stop="onMoreChevronClick"
                        >
                            <ChevronDown class="size-3" />
                        </button>
                    </div>

                    <div
                        v-show="moreOpen"
                        class="absolute top-full left-0 z-50 h-2 w-36"
                    />

                    <div
                        v-show="moreOpen"
                        class="absolute top-[calc(100%+0.5rem)] left-0 z-50 w-36 rounded-md border border-black/[0.08] bg-card py-1 shadow-md dark:border-white/[0.08]"
                    >
                        <Link
                            :href="upcoming()"
                            class="flex items-center gap-2 px-3 py-1.5 text-sm transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                            :class="
                                urlIsActive(upcoming(), page.url)
                                    ? 'text-foreground'
                                    : 'text-muted-foreground'
                            "
                            @click="moreOpen = false"
                        >
                            <Calendar class="size-3.5" />
                            Upcoming
                        </Link>
                        <Link
                            :href="history()"
                            class="flex items-center gap-2 px-3 py-1.5 text-sm transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                            :class="
                                urlIsActive(history(), page.url)
                                    ? 'text-foreground'
                                    : 'text-muted-foreground'
                            "
                            @click="moreOpen = false"
                        >
                            <History class="size-3.5" />
                            History
                        </Link>
                    </div>
                </div>

                <!-- Recurring -->
                <Link
                    :href="recurringTemplates()"
                    class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        urlIsActive(recurringTemplates(), page.url)
                            ? 'bg-black/[0.06] text-foreground dark:bg-white/[0.08] dark:text-foreground'
                            : 'text-muted-foreground'
                    "
                >
                    <Repeat class="size-3.5" />
                    Recurring
                </Link>

                <!-- Plan -->
                <Link
                    :href="planIndex().url"
                    class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        page.url.startsWith('/plan')
                            ? 'bg-black/[0.06] text-foreground dark:bg-white/[0.08] dark:text-foreground'
                            : 'text-muted-foreground'
                    "
                >
                    <LayoutList class="size-3.5" />
                    Planner
                </Link>

                <!-- Labels hover dropdown -->
                <div
                    class="relative"
                    @mouseenter="onLabelsMouseEnter"
                    @mouseleave="labelsOpen = false"
                >
                    <div
                        class="flex items-center rounded-md transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                        :class="
                            page.url.startsWith('/labels')
                                ? 'bg-black/[0.06] text-foreground dark:bg-white/[0.08] dark:text-foreground'
                                : 'text-muted-foreground'
                        "
                    >
                        <Link
                            :href="getLabels()"
                            class="flex items-center gap-1.5 rounded-l-md py-1.5 pr-2 pl-3 text-sm font-medium outline-none"
                        >
                            <Tag class="size-3.5" />
                            Labels
                        </Link>
                        <button
                            type="button"
                            class="flex cursor-pointer items-center rounded-r-md py-1.5 pr-2 pl-0.5 outline-none"
                            @touchstart.passive="onLabelsChevronTouch"
                            @click.stop="onLabelsChevronClick"
                        >
                            <ChevronDown class="size-3" />
                        </button>
                    </div>

                    <!-- invisible bridge so fast mouse movement doesn't leave the hover zone -->
                    <div
                        v-show="labelsOpen"
                        class="absolute top-full left-0 z-50 h-2 w-48"
                    />

                    <div
                        v-show="labelsOpen"
                        class="absolute top-[calc(100%+0.5rem)] left-0 z-50 w-48 rounded-md border border-black/[0.08] bg-card py-1 shadow-md dark:border-white/[0.08]"
                    >
                        <Link
                            v-for="label in labels"
                            :key="label.id"
                            :href="getLabel(label.id)"
                            class="flex items-center gap-2 px-3 py-1.5 text-sm transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                            :class="
                                toUrl(getLabel(label.id)) === page.url
                                    ? 'text-foreground'
                                    : 'text-muted-foreground'
                            "
                            @click="labelsOpen = false"
                        >
                            <Circle class="size-3" />
                            {{ label.name }}
                        </Link>
                        <div
                            v-if="labels.length > 0"
                            class="my-1 border-t border-black/[0.06] dark:border-white/[0.06]"
                        />
                        <Link
                            :href="getLabels()"
                            class="flex items-center gap-2 px-3 py-1.5 text-sm transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                            :class="
                                toUrl(getLabels()) === page.url
                                    ? 'text-foreground'
                                    : 'text-muted-foreground'
                            "
                            @click="labelsOpen = false"
                        >
                            <Tag class="size-3.5" />
                            Manage Labels
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Right side: user menu + mobile toggle -->
            <div class="flex items-center gap-2">
                <!-- User dropdown -->
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button
                            class="flex size-8 cursor-pointer items-center justify-center rounded-full border border-black/[0.1] bg-card text-xs font-semibold text-foreground transition-colors hover:bg-black/[0.04] dark:border-white/[0.1] dark:bg-elevated dark:hover:bg-white/[0.06]"
                        >
                            {{ getInitials(auth.user?.name) }}
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <UserMenuContent :user="auth.user" />
                    </DropdownMenuContent>
                </DropdownMenu>

                <!-- Mobile hamburger -->
                <button
                    class="flex size-8 items-center justify-center rounded-md transition-colors hover:bg-black/5 md:hidden dark:hover:bg-white/5"
                    @click="mobileOpen = !mobileOpen"
                >
                    <X v-if="mobileOpen" class="size-5" />
                    <Menu v-else class="size-5" />
                </button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <div
            v-if="mobileOpen"
            class="border-t border-black/[0.06] px-6 py-3 md:hidden dark:border-white/[0.06]"
        >
            <nav class="flex flex-col gap-1">
                <Link
                    v-for="item in navItems"
                    :key="item.title"
                    :href="item.href"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        isCurrentRoute(item)
                            ? 'bg-black/[0.06] text-foreground dark:bg-white/[0.08] dark:text-foreground'
                            : 'text-muted-foreground'
                    "
                    @click="mobileOpen = false"
                >
                    <component :is="item.icon" class="size-4" />
                    {{ item.title }}
                </Link>
                <Link
                    :href="upcoming()"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        urlIsActive(upcoming(), page.url)
                            ? 'text-foreground'
                            : 'text-muted-foreground'
                    "
                    @click="mobileOpen = false"
                >
                    <Calendar class="size-4" />
                    Upcoming
                </Link>
                <Link
                    :href="history()"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        urlIsActive(history(), page.url)
                            ? 'text-foreground'
                            : 'text-muted-foreground'
                    "
                    @click="mobileOpen = false"
                >
                    <History class="size-4" />
                    History
                </Link>
                <Link
                    :href="recurringTemplates()"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        urlIsActive(recurringTemplates(), page.url)
                            ? 'bg-black/[0.06] text-foreground dark:bg-white/[0.08] dark:text-foreground'
                            : 'text-muted-foreground'
                    "
                    @click="mobileOpen = false"
                >
                    <Repeat class="size-4" />
                    Recurring
                </Link>
                <Link
                    :href="planIndex().url"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        page.url.startsWith('/plan')
                            ? 'bg-black/[0.06] text-foreground dark:bg-white/[0.08] dark:text-foreground'
                            : 'text-muted-foreground'
                    "
                    @click="mobileOpen = false"
                >
                    <LayoutList class="size-4" />
                    Planner
                </Link>
                <div
                    class="my-1 border-t border-black/[0.06] dark:border-white/[0.06]"
                />
                <Link
                    v-for="label in labels"
                    :key="label.id"
                    :href="getLabel(label.id)"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        toUrl(getLabel(label.id)) === page.url
                            ? 'text-foreground'
                            : 'text-muted-foreground'
                    "
                    @click="mobileOpen = false"
                >
                    <Circle class="size-3.5" />
                    {{ label.name }}
                </Link>
                <Link
                    :href="getLabels()"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                    :class="
                        toUrl(getLabels()) === page.url
                            ? 'text-foreground'
                            : 'text-muted-foreground'
                    "
                    @click="mobileOpen = false"
                >
                    <Tag class="size-4" />
                    Manage Labels
                </Link>
            </nav>
        </div>
    </header>
</template>
