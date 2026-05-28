<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { show as getLabel, index as getLabels } from '@/routes/labels';
import { index as recurringTemplates } from '@/routes/recurring';
import { history, today, upcoming } from '@/routes/tasks';
import { type AppPageProps, type NavSection } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Calendar, Circle, History, Repeat, Star, Tag } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

type Label = {
    id: number;
    name: string;
};

const page = usePage<AppPageProps<{ labels: Label[] }>>();

const navSections = computed<NavSection[]>(() => [
    {
        title: 'Tasks',
        items: [
            {
                title: 'Today',
                href: today(),
                icon: Star,
            },
            {
                title: 'Upcoming',
                href: upcoming(),
                icon: Calendar,
            },
        ],
    },

    {
        title: 'Setup',
        items: [
            {
                title: 'Recurring',
                href: recurringTemplates(),
                icon: Repeat,
            },
        ],
    },

    {
        title: 'Activity',
        items: [
            {
                title: 'History',
                href: history(),
                icon: History,
            },
        ],
    },

    {
        title: 'Labels',
        items: [
            ...page.props.labels.map((label) => ({
                title: label.name,
                href: getLabel(label.id),
                icon: Circle,
            })),
            {
                title: 'Manage Labels',
                href: getLabels(),
                icon: Tag,
            },
        ],
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="today()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain
                v-for="section in navSections"
                :key="section.title"
                :items="section.items"
                :title="section.title"
            />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
