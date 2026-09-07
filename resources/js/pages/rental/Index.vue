<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CalendarDays, CarFront, Check, Fuel, Gauge, MapPin, Users } from 'lucide-vue-next';
import { ref } from 'vue';

const days = ref(3);
const selected = ref('Toyota Innova');
const cars = [
    { name: 'Toyota Vios', seats: 5, transmission: 'Tự động', fuel: 'Xăng', price: '650.000đ' },
    { name: 'Toyota Innova', seats: 7, transmission: 'Tự động', fuel: 'Xăng', price: '900.000đ' },
    { name: 'Kia Carnival', seats: 7, transmission: 'Tự động', fuel: 'Dầu', price: '1.450.000đ' },
    { name: 'VinFast VF 9', seats: 7, transmission: 'Tự động', fuel: 'Điện', price: '1.800.000đ' },
];
</script>

<template>
    <Head title="Thuê xe" />
    <div class="min-h-screen bg-slate-50 text-slate-950">
        <header class="border-b border-slate-200 bg-white"><div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8"><span class="grid size-9 place-items-center rounded-xl bg-slate-950 text-white"><CarFront class="size-5" /></span><span class="font-black">CarBooking</span><span class="mx-2 hidden text-slate-300 sm:block">/</span><span class="hidden text-sm font-semibold text-slate-500 sm:block">Thuê xe</span></div></header>
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-7"><p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Thuê xe theo ngày</p><h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Xe phù hợp cho mọi kế hoạch</h1><p class="mt-2 max-w-2xl text-slate-500">Chọn xe, số ngày và thời gian nhận xe. Bạn sẽ thấy tổng tiền rõ ràng trước khi xác nhận.</p></div>
            <div class="mb-7 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4 md:p-5">
                <label><span class="mb-1.5 block text-xs font-bold uppercase text-slate-400">Khu vực nhận xe</span><div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3"><MapPin class="size-5 text-indigo-600" /><input class="w-full border-0 bg-transparent p-0 text-sm font-semibold outline-none focus:ring-0" value="Đà Nẵng" /></div></label>
                <label><span class="mb-1.5 block text-xs font-bold uppercase text-slate-400">Ngày nhận</span><div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3"><CalendarDays class="size-5 text-indigo-600" /><input type="date" class="w-full border-0 bg-transparent p-0 text-sm outline-none focus:ring-0" /></div></label>
                <label><span class="mb-1.5 block text-xs font-bold uppercase text-slate-400">Số ngày thuê</span><div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3"><button @click="days = Math.max(1, days - 1)" class="grid size-8 place-items-center rounded-lg bg-white shadow-sm">−</button><span class="flex-1 text-center text-sm font-bold">{{ days }} ngày</span><button @click="days++" class="grid size-8 place-items-center rounded-lg bg-white shadow-sm">+</button></div></label>
                <button class="self-end rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-bold text-white hover:bg-indigo-500">Tìm xe</button>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <article v-for="car in cars" :key="car.name" class="overflow-hidden rounded-3xl border bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg" :class="selected === car.name ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200'"><button @click="selected = car.name" class="w-full text-left"><div class="relative flex aspect-[4/3] items-end bg-gradient-to-br from-slate-100 to-slate-300 p-5"><span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-black">Có sẵn</span><CarFront class="absolute right-5 top-5 size-7 text-slate-500" /><div><h2 class="text-xl font-black text-slate-700">{{ car.name }}</h2><p class="mt-1 text-sm text-slate-500">{{ car.seats }} chỗ · {{ car.transmission }}</p></div></div><div class="p-5"><div class="grid grid-cols-3 gap-2 border-b border-slate-100 pb-4 text-xs text-slate-500"><span class="inline-flex items-center gap-1"><Users class="size-4" />{{ car.seats }} chỗ</span><span class="inline-flex items-center gap-1"><Gauge class="size-4" />{{ car.transmission }}</span><span class="inline-flex items-center gap-1"><Fuel class="size-4" />{{ car.fuel }}</span></div><div class="mt-4 flex items-end justify-between"><div><p class="text-xs text-slate-400">Từ</p><p class="text-lg font-black">{{ car.price }}<span class="text-xs font-medium text-slate-400">/ngày</span></p></div><span v-if="selected === car.name" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600"><Check class="size-4" /> Đã chọn</span></div></div></button></article>
            </div>
            <div class="mt-8 flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6"><div><p class="text-sm text-slate-500">Bạn đang chọn</p><p class="mt-1 text-xl font-black">{{ selected }} · {{ days }} ngày</p></div><div class="flex items-center gap-5"><div class="text-right"><p class="text-xs text-slate-400">Tạm tính</p><p class="text-xl font-black">{{ selected === 'Kia Carnival' ? '4.350.000đ' : selected === 'VinFast VF 9' ? '5.400.000đ' : selected === 'Toyota Vios' ? '1.950.000đ' : '2.700.000đ' }}</p></div><button class="rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white hover:bg-slate-800">Tiếp tục</button></div></div>
        </main>
    </div>
</template>
