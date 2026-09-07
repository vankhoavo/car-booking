<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, CalendarCheck, Car, FileText, FolderGit2, KeyRound, LayoutDashboard } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');
const dashboardUrl = computed(() => isAdmin.value ? '/admin' : (page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/'));

const mainNavItems = computed<NavItem[]>(() => isAdmin.value ? [
    { title: 'Tổng quan', href: '/admin', icon: LayoutDashboard },
    { title: 'Đơn đặt xe', href: '/admin/bookings', icon: CalendarCheck },
    { title: 'Đơn thuê xe', href: '/admin/rentals', icon: KeyRound },
    { title: 'Quản lý xe', href: '/admin/vehicles', icon: Car },
    { title: 'Quản lý Blog', href: '/admin/blog', icon: FileText },
] : [
    { title: 'Dashboard', href: dashboardUrl.value, icon: LayoutDashboard },
]);

const footerNavItems: NavItem[] = [
    { title: 'Repository', href: 'https://github.com/laravel/vue-starter-kit', icon: FolderGit2 },
    { title: 'Documentation', href: 'https://laravel.com/docs/starter-kits#vue', icon: BookOpen },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <SidebarMenu v-if="!isAdmin">
                <SidebarMenuItem>
                    <TeamSwitcher />
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
