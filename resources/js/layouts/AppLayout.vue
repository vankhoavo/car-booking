<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayoutWithSidebar from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const page = usePage();
const isPublicForm = computed(() => {
    const path = page.url.split('?')[0];
    return path === '/dat-xe' || path === '/thue-xe';
});
</script>

<template>
    <div v-if="isPublicForm" class="min-h-svh w-full min-w-0 bg-slate-50/80">
        <slot />
    </div>
    <AppLayoutWithSidebar v-else :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayoutWithSidebar>
</template>
