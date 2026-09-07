<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type Team={id:number;name:string;slug:string};
const props=defineProps<{teams:Team[]}>();
const breadcrumbs:BreadcrumbItem[]=[{title:'Teams',href:'/teams'}];
</script>
<template><AppLayout :breadcrumbs="breadcrumbs"><Head title="Teams"/><div class="flex min-w-0 flex-col gap-4 p-3 sm:p-6 lg:p-8"><div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><h1 class="text-xl font-semibold">Teams</h1><p class="text-sm text-muted-foreground">Quản lý các nhóm của bạn.</p></div><Link :href="dashboard()" class="rounded-md bg-primary px-4 py-2 text-center text-sm font-medium text-primary-foreground">Dashboard</Link></div><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"> <Link v-for="team in props.teams" :key="team.id" :href="`/teams/${team.id}/edit`" class="min-w-0 rounded-lg border p-4 transition hover:bg-muted/50"><div class="truncate font-medium">{{team.name}}</div><div class="truncate text-sm text-muted-foreground">{{team.slug}}</div></Link><div v-if="!props.teams.length" class="rounded-lg border p-6 text-center text-sm text-muted-foreground sm:col-span-2 lg:col-span-3">Chưa có nhóm.</div></div></div></AppLayout></template>
