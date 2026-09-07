<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ArrowRight, CalendarDays, CarFront, Check, Clock3, MapPin, Users } from 'lucide-vue-next';
import { ref } from 'vue';

const selected = ref('Tiêu chuẩn');
const cars = [
    { name: 'Toyota Vios', type: '4 chỗ · Tiêu chuẩn', price: '320.000đ', note: 'Giá dự kiến / chuyến' },
    { name: 'Toyota Innova', type: '7 chỗ · Gia đình', price: '520.000đ', note: 'Giá dự kiến / chuyến' },
    { name: 'Kia Carnival', type: '7 chỗ · Cao cấp', price: '780.000đ', note: 'Giá dự kiến / chuyến' },
];
</script>

<template>
    <Head title="Đặt xe" />
    <div class="min-h-screen bg-slate-50 text-slate-950">
        <header class="border-b border-slate-200 bg-white"><div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8"><span class="grid size-9 place-items-center rounded-xl bg-slate-950 text-white"><CarFront class="size-5" /></span><span class="font-black">CarBooking</span><span class="mx-2 hidden text-slate-300 sm:block">/</span><span class="hidden text-sm font-semibold text-slate-500 sm:block">Đặt xe</span></div></header>
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[.85fr_1.15fr]">
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between"><div><p class="text-sm font-bold text-indigo-600">BƯỚC 1</p><h1 class="mt-1 text-2xl font-black">Nhập hành trình</h1></div><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">Đặt ngay</span></div>
                    <div class="mt-6 space-y-4">
                        <label class="block"><span class="mb-1.5 block text-sm font-semibold">Điểm đón</span><div class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3"><MapPin class="size-5 text-indigo-600" /><input class="w-full border-0 p-0 text-sm outline-none focus:ring-0" value="Sân bay Đà Nẵng" /></div></label>
                        <label class="block"><span class="mb-1.5 block text-sm font-semibold">Điểm đến</span><div class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3"><ArrowRight class="size-5 text-indigo-600" /><input class="w-full border-0 p-0 text-sm outline-none focus:ring-0" value="Hội An, Quảng Nam" /></div></label>
                        <div class="grid gap-3 sm:grid-cols-2"><label><span class="mb-1.5 block text-sm font-semibold">Ngày</span><div class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3"><CalendarDays class="size-5 text-indigo-600" /><input type="date" class="w-full border-0 p-0 text-sm outline-none focus:ring-0" /></div></label><label><span class="mb-1.5 block text-sm font-semibold">Giờ</span><div class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3"><Clock3 class="size-5 text-indigo-600" /><input type="time" value="09:00" class="w-full border-0 p-0 text-sm outline-none focus:ring-0" /></div></label></div>
                        <label><span class="mb-1.5 block text-sm font-semibold">Số hành khách</span><div class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3"><Users class="size-5 text-indigo-600" /><select class="w-full border-0 bg-transparent p-0 text-sm outline-none focus:ring-0"><option>2 người</option><option>3 người</option><option>4 người</option><option>5 người</option><option>6 người</option><option>7 người</option></select></div></label>
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"><div><p class="text-sm font-bold text-indigo-600">BƯỚC 2</p><h2 class="mt-1 text-2xl font-black">Chọn loại xe</h2><p class="mt-1 text-sm text-slate-500">Giá cuối cùng sẽ được xác nhận trước khi thanh toán.</p></div><div class="mt-6 space-y-3"><button v-for="car in cars" :key="car.name" @click="selected = car.type.split(' · ')[1]" class="flex w-full items-center justify-between gap-4 rounded-2xl border p-4 text-left transition" :class="selected === car.type.split(' · ')[1] ? 'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-100' : 'border-slate-200 hover:border-slate-300'"><div class="flex min-w-0 items-center gap-4"><div class="grid size-16 shrink-0 place-items-center rounded-2xl bg-slate-100"><CarFront class="size-8 text-slate-500" /></div><div class="min-w-0"><div class="flex items-center gap-2"><h3 class="font-black">{{ car.name }}</h3><span v-if="selected === car.type.split(' · ')[1]" class="rounded-full bg-indigo-600 p-1 text-white"><Check class="size-3" /></span></div><p class="mt-1 text-sm text-slate-500">{{ car.type }}</p><p class="mt-1 text-xs text-slate-400">{{ car.note }}</p></div></div><div class="shrink-0 text-right"><p class="font-black">{{ car.price }}</p><p class="mt-1 text-xs text-slate-400">/chuyến</p></div></button></div><div class="mt-6 rounded-2xl bg-slate-50 p-4"><div class="flex items-center justify-between text-sm"><span class="text-slate-500">Tổng tạm tính</span><span class="font-black">520.000đ</span></div><button class="mt-4 w-full rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-bold text-white hover:bg-indigo-500">Tiếp tục đặt xe</button></div></section>
            </div>
        </main>
    </div>
</template>
