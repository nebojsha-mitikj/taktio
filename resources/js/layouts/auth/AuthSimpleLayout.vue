<script setup lang="ts">
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

withDefaults(defineProps<{
    title?: string;
    description?: string;
    navHref?: string;
    navLabel?: string;
}>(), {
    title: '',
    description: '',
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-page text-foreground">

        <!-- Navbar -->
        <header class="sticky top-0 z-10 border-b border-white/[0.06] bg-page/80 backdrop-blur-md">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4 sm:px-8">
                <Link :href="home()" class="text-xl font-bold text-foreground transition-all hover:opacity-70 hover:scale-[1.02]">Taktio</Link>
                <Link
                    v-if="navHref && navLabel"
                    :href="navHref"
                    class="rounded-lg bg-brand px-4 py-1.5 text-sm font-medium text-page transition-all hover:bg-brand-hover"
                >
                    {{ navLabel }}
                </Link>
            </div>
        </header>

        <!-- Form area -->
        <div class="flex flex-1 flex-col items-center justify-center px-6 py-16">
            <div class="w-full max-w-sm">
                <div class="mb-8 text-center">
                    <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-foreground">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
