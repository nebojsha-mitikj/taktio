<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster
            position="top-right"
            close-button
            close-button-position="top-right"
            :offset="{ top: '90px' }"
        />
        <slot />
    </AppLayout>
</template>
