<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { CarFront, ShieldCheck } from 'lucide-vue-next';

defineProps<{ title?: string; description?: string }>();
</script>

<template>
    <div class="relative flex min-h-svh overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(99,102,241,.28),transparent_32%),radial-gradient(circle_at_90%_80%,rgba(14,165,233,.18),transparent_30%)]" />
        <div class="relative mx-auto grid w-full max-w-6xl items-center gap-10 px-4 py-8 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div class="hidden lg:block">
                <Link :href="home()" class="inline-flex items-center gap-3 font-black text-xl">
                    <span class="grid size-11 place-items-center rounded-2xl bg-white text-slate-950 shadow-xl"><CarFront class="size-6" /></span>
                    Car<span class="text-indigo-300">Booking</span>
                </Link>
                <div class="mt-16 max-w-xl">
                    <p class="text-sm font-bold uppercase tracking-[0.22em] text-indigo-300">Khu vực quản lý</p>
                    <h2 class="mt-4 text-5xl font-black leading-tight tracking-tight">Điều hành hành trình đơn giản hơn.</h2>
                    <p class="mt-6 text-lg leading-8 text-slate-300">Đăng nhập để quản lý yêu cầu đặt xe, thuê xe và các thiết lập tài khoản.</p>
                    <div class="mt-10 inline-flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                        <ShieldCheck class="size-5 text-emerald-300" /> Bảo mật bằng email và mật khẩu
                    </div>
                </div>
            </div>
            <div class="mx-auto w-full max-w-md rounded-3xl border border-white/10 bg-white p-6 text-slate-950 shadow-2xl shadow-black/30 sm:p-8">
                <div class="mb-7 flex flex-col items-center text-center lg:hidden">
                    <Link :href="home()" class="grid size-11 place-items-center rounded-2xl bg-slate-950 text-white shadow-lg">
                        <CarFront class="size-6" />
                    </Link>
                    <span class="mt-3 font-black">Car<span class="text-indigo-600">Booking</span></span>
                </div>
                <div class="space-y-2 text-center">
                    <h1 class="text-2xl font-black tracking-tight">{{ title }}</h1>
                    <p class="text-muted-foreground text-sm leading-6">{{ description }}</p>
                </div>
                <div class="mt-7"><slot /></div>
            </div>
        </div>
    </div>
</template>
